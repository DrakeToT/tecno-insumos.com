<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/sanitize.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/permisos.php';

class ProfileController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    /**
     * Renderiza la vista de perfil
     */
    public function index() {
        if (!isUserLoggedIn()) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $user = currentUser();

        // Define si el usuario puede ver el botón de editar
        // Esta variable estará disponible en la vista 'views/perfil/index.php'
        $puedeEditar = Permisos::tienePermiso('editar_perfil');

        require_once __DIR__ . '/../views/perfil/index.php';
    }

    // ====================================================================
    // API REST METHODS
    // ====================================================================

    /**
     * GET: Obtener datos del usuario actual
     */
    public function getProfile() {
        $this->checkAuth();
        $user = currentUser();
        $idUsuario = $user['id'];

        try {
            // Buscar datos actuales en la DB
            $userDb = $this->userModel->findById($idUsuario);

            if (!$userDb) {
                $this->jsonResponse(["success" => false, "message" => "Usuario no encontrado."], 404);
            }

            // Validar imagen física
            if (!empty($userDb['fotoPerfil'])) {
                $fotoPath = __DIR__ . '/../../public/assets/uploads/' . $userDb['fotoPerfil'];
                if (!file_exists($fotoPath)) {
                    $userDb['fotoPerfil'] = null;
                }
            }

            // Refrescar sesión con datos nuevos
            $_SESSION['user'] = buildUserSessionArray($userDb);

            $this->jsonResponse(["success" => true, "user" => $_SESSION['user']]);

        } catch (Exception $e) {
            $this->jsonResponse(["success" => false, "message" => "Error: " . $e->getMessage()], 500);
        }
    }

    /**
     * PUT: Actualizar datos personales (Nombre, Apellido, Email)
     */
    public function updateData() {
        $this->checkAuth();

        if (!Permisos::tienePermiso('editar_perfil')) {
            $this->jsonResponse(["success" => false, "message" => "No tienes permiso para editar tus datos personales."], 403);
        }
        
        $user = currentUser();
        $idUsuario = $user['id'];

        $input = json_decode(file_get_contents("php://input"), true);
        
        $nombre   = sanitizeInput($input['nombre'] ?? '');
        $apellido = sanitizeInput($input['apellido'] ?? '');
        $email    = sanitizeInput($input['email'] ?? '');

        if (empty($nombre) || empty($apellido) || empty($email)) {
            $this->jsonResponse(["success" => false, "message" => "Todos los campos son obligatorios."], 400);
        }

        try {
            if ($this->userModel->updateProfile($idUsuario, $nombre, $apellido, $email)) {
                // Refrescar sesión
                $updatedUser = $this->userModel->findById($idUsuario);
                $_SESSION['user'] = buildUserSessionArray($updatedUser);

                $this->jsonResponse(["success" => true, "message" => "Datos actualizados correctamente."]);
            } else {
                $this->jsonResponse(["success" => false, "message" => "No se pudo actualizar el perfil."], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(["success" => false, "message" => "Error: " . $e->getMessage()], 500);
        }
    }

    /**
     * POST: Subir nueva foto de perfil
     */
    public function uploadPhoto() {
        $this->checkAuth();
        $user = currentUser();
        $idUsuario = $user['id'];

        if (!isset($_FILES['fotoPerfil'])) {
            $this->jsonResponse(["success" => false, "message" => "No se envió ninguna imagen."], 400);
        }

        $file = $_FILES['fotoPerfil'];
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $extensionesPermitidas)) {
            $this->jsonResponse(["success" => false, "message" => "Formato no permitido. Use JPG, PNG o WEBP."], 400);
        }

        $nombreArchivo = 'perfil_' . $idUsuario . '_' . time() . '.' . $ext;
        $rutaDestino = __DIR__ . '/../../public/assets/uploads/' . $nombreArchivo;

        // Borrar foto anterior si existe
        if (!empty($user['fotoPerfil'])) {
            $fotoAnterior = __DIR__ . '/../../public/assets/uploads/' . $user['fotoPerfil'];
            if (file_exists($fotoAnterior)) {
                @unlink($fotoAnterior);
            }
        }

        if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
            // Actualizar BD y Sesión
            $this->userModel->updatePhoto($idUsuario, $nombreArchivo);
            $_SESSION['user']['fotoPerfil'] = $nombreArchivo;

            $this->jsonResponse(["success" => true, "message" => "Foto actualizada.", "file" => $nombreArchivo]);
        } else {
            $this->jsonResponse(["success" => false, "message" => "Error al guardar la imagen en el servidor."], 500);
        }
    }

    /**
     * PATCH: Cambiar contraseña
     */
    public function changePassword() {
        $this->checkAuth();
        $user = currentUser();
        $idUsuario = $user['id'];

        $input = json_decode(file_get_contents("php://input"), true);
        $actualPassword = trim($input['actualPassword'] ?? '');
        $newPassword    = trim($input['newPassword'] ?? '');

        if (empty($actualPassword) || empty($newPassword)) {
            $this->jsonResponse(["success" => false, "message" => "Complete todos los campos."], 400);
        }

        try {
            // Verificar password actual
            $userDb = $this->userModel->findById($idUsuario);
            if (!$userDb || !password_verify($actualPassword, $userDb['password'])) {
                $this->jsonResponse(["success" => false, "message" => "La contraseña actual es incorrecta."], 401);
            }

            // Actualizar nueva contraseña
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            if ($this->userModel->updatePassword($idUsuario, $newHash)) {
                $this->jsonResponse(["success" => true, "message" => "Contraseña actualizada correctamente."]);
            } else {
                $this->jsonResponse(["success" => false, "message" => "Error al actualizar la contraseña."], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(["success" => false, "message" => "Error: " . $e->getMessage()], 500);
        }
    }

    // Helpers
    private function checkAuth() {
        if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');
        if (!isUserLoggedIn()) {
            echo json_encode(["success" => false, "message" => "Sesión caducada."]);
            http_response_code(401);
            exit;
        }
    }

    private function jsonResponse($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
?>