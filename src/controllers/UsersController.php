<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/sanitize.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/permisos.php';

class UsersController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Renderiza la vista
     * Protegido visualmente con Error 403
     */
    public function index()
    {
        if (!isUserLoggedIn()) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Validación de permiso para ver la pantalla
        if (!Permisos::tienePermiso('ver_usuarios')) {
            http_response_code(403);
            require_once __DIR__ . '/../views/errors/403.php';
            exit;
        }

        require_once __DIR__ . '/../views/usuarios/index.php';
    }

    // ====================================================================
    // API REST METHODS
    // ====================================================================

    /**
     * GET: Obtener un usuario específico por ID
     * Permiso: acceder_usuarios
     */
    public function getOne()
    {
        checkAuth();

        if (!Permisos::tienePermiso('acceder_usuarios')) {
            $this->jsonResponse(['success' => false, 'message' => 'No tiene permiso para ver usuarios.'], 403);
        }

        $idUsuario = isset($_GET['id']) ? sanitizeInt($_GET['id']) : 0;

        if ($idUsuario <= 0) {
            $this->jsonResponse(["success" => false, "message" => "ID inválido."], 400);
        }

        $usuario = $this->userModel->findById($idUsuario);

        if ($usuario) {
            $this->jsonResponse(["success" => true, "usuario" => $usuario]);
        } else {
            $this->jsonResponse(["success" => false, "message" => "Usuario no encontrado."], 404);
        }
    }

    /**
     * GET: Listar usuarios con filtros y paginación
     * Permisos: listar_usuarios, listar_usuarios_activos
     */
    public function getAll()
    {
        checkAuth();
        
        switch (true){
            case Permisos::tienePermiso('listar_usuarios'):
                // Puede ver todos los usuarios
                $search = sanitizeInput($_GET['search'] ?? '');
                $sort   = sanitizeInput($_GET['sort'] ?? 'id');
                $order  = sanitizeInput($_GET['order'] ?? 'ASC');
                $limit  = isset($_GET['limit']) ? sanitizeInt($_GET['limit']) : 10;
                $page   = isset($_GET['page']) ? sanitizeInt($_GET['page']) : 1;
                $offset = ($page - 1) * $limit;
        
                $allowedSort = ['id', 'nombre', 'apellido', 'email', 'estado', 'rol'];
                if (!in_array($sort, $allowedSort)) $sort = 'id';
                $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        
                try {
                    $usuarios = $this->userModel->getAll($search, $sort, $order, $limit, $offset);
                    $totalUsuarios = $this->userModel->countAll($search);
                    $totalPages = ceil($totalUsuarios / $limit);
        
                    $this->jsonResponse([
                        'success' => true,
                        'data' => $usuarios,
                        'pagination' => [
                            'page' => $page,
                            'pages' => $totalPages,
                            'total' => $totalUsuarios,
                            'limit' => $limit
                        ]
                    ]);
                } catch (Exception $e) {
                    $this->jsonResponse(['success' => false, 'message' => 'Error al obtener datos.'], 500);
                }
                break;
            case Permisos::tienePermiso('listar_usuarios_activos'):
                // Puede ver solo usuarios activos
                try {
                    $usuarios = $this->userModel->getAllActive();
                    $this->jsonResponse(['success' => true, 'data' => $usuarios]);
                } catch (Exception $e) {
                    $this->jsonResponse(['success' => false, 'message' => 'Error al cargar usuarios'], 500);
                }
                break;
            default:
                $this->jsonResponse(['success' => false, 'message' => 'Acceso denegado.'], 403);
        }
    }

    /**
     * POST: Crear Usuario
     * Permiso: crear_usuario
     */
    public function create()
    {
        checkAuth();

        if (!Permisos::tienePermiso('crear_usuario')) {
            $this->jsonResponse(["success" => false, "message" => "No tiene permiso para crear usuarios."], 403);
        }

        $input = json_decode(file_get_contents("php://input"), true);

        $nombre   = sanitizeInput($input['nombre'] ?? '');
        $apellido = sanitizeInput($input['apellido'] ?? '');
        $email    = sanitizeInput($input['email'] ?? '');
        $password = sanitizeInput($input['password'] ?? '');
        $idRol    = isset($input['rol']) ? sanitizeInt($input['rol']) : 0;
        $estado   = sanitizeInput($input['estado'] ?? 'Activo');

        // Validaciones
        if (empty($nombre) || empty($apellido) || empty($email) || empty($password) || $idRol <= 0) {
            $this->jsonResponse(["success" => false, "message" => "Todos los campos son obligatorios."], 400);
        }
        if (!validateEmail($email)) {
            $this->jsonResponse(["success" => false, "message" => "Correo electrónico inválido."], 400);
        }
        if (!validateLength($password, 255, 8)) {
            $this->jsonResponse(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres."], 400);
        }
        if ($this->userModel->findByEmail($email)) {
            $this->jsonResponse(["success" => false, "message" => "El correo electrónico ya está registrado."], 409);
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'password' => $hashedPassword,
            'idRol' => $idRol,
            'estado' => $estado
        ];

        if ($this->userModel->create($data)) {
            $this->jsonResponse(["success" => true, "message" => "Usuario creado correctamente."]);
        } else {
            $this->jsonResponse(["success" => false, "message" => "Error al crear el usuario."], 500);
        }
    }

    /**
     * PUT: Editar Usuario (Datos completos)
     * Permiso: editar_usuario
     */
    public function update()
    {
        checkAuth();

        if (!Permisos::tienePermiso('editar_usuario')) {
            $this->jsonResponse(["success" => false, "message" => "No tiene permiso para editar usuarios."], 403);
        }

        $input = json_decode(file_get_contents("php://input"), true);

        $idUsuario = isset($input['id']) ? sanitizeInt($input['id']) : 0;
        $nombre    = sanitizeInput($input['nombre'] ?? '');
        $apellido  = sanitizeInput($input['apellido'] ?? '');
        $email     = sanitizeInput($input['email'] ?? '');
        $rol       = isset($input['rol']) ? sanitizeInt($input['rol']) : 0;
        $estado    = sanitizeInput($input['estado'] ?? 'Activo');

        if ($idUsuario <= 0 || empty($nombre) || empty($apellido) || empty($email) || $rol <= 0) {
            $this->jsonResponse(["success" => false, "message" => "Todos los campos son obligatorios."], 400);
        }

        if (!validateEmail($email)) {
            $this->jsonResponse(["success" => false, "message" => "Correo electrónico inválido."], 400);
        }

        // Verificar duplicados (email usado por otro ID)
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && (int)$existingUser['id'] !== $idUsuario) {
            $this->jsonResponse(["success" => false, "message" => "El correo electrónico ya pertenece a otro usuario."], 409);
        }

        $data = [
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'email'    => $email,
            'idRol'    => $rol,
            'estado'   => $estado
        ];

        if ($this->userModel->update($idUsuario, $data)) {
            // Si se editó a sí mismo, refrescar sesión
            $currentUser = currentUser();
            $response = ["success" => true, "message" => "Usuario actualizado correctamente."];

            if ($currentUser && $idUsuario === $currentUser['id']) {
                refreshUserSession($idUsuario);
                $response["redirect"] = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
            }
            $this->jsonResponse($response);
        } else {
            $this->jsonResponse(["success" => false, "message" => "No se pudieron guardar los cambios."], 500);
        }
    }

    /**
     * PATCH: Cambiar Estado o Resetear Password
     * Permiso: editar_usuario
     */
    public function changeStatusOrPassword()
    {
        checkAuth();

        if (!Permisos::tienePermiso('editar_usuario')) {
            $this->jsonResponse(["success" => false, "message" => "No tiene permiso para modificar usuarios."], 403);
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $idUsuario = isset($input['id']) ? sanitizeInt($input['id']) : 0;
        $accion = sanitizeInput($input['accion'] ?? '');

        if ($idUsuario <= 0 || empty($accion)) {
            $this->jsonResponse(["success" => false, "message" => "Datos incompletos."], 400);
        }

        // 1. Acción: Cambiar Estado
        if ($accion === 'estado') {
            $currentUser = currentUser();
            if ($currentUser && $idUsuario === $currentUser['id']) {
                $this->jsonResponse(["success" => false, "message" => "No puede cambiar el estado de su propio usuario."], 400);
            }

            $nuevoEstado = sanitizeInput($input['estado'] ?? '');
            if (!in_array($nuevoEstado, ['Activo', 'Inactivo'])) {
                $this->jsonResponse(["success" => false, "message" => "Estado inválido."], 400);
            }

            if ($this->userModel->changeStatus($idUsuario, $nuevoEstado)) {
                $this->jsonResponse(["success" => true, "message" => "Estado actualizado correctamente."]);
            } else {
                $this->jsonResponse(["success" => false, "message" => "Error al actualizar el estado."], 500);
            }
        }

        // 2. Acción: Reset Password
        if ($accion === 'reset-password') {
            $nuevaPassword = 'TempPass' . rand(1000, 9999);
            $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

            if ($this->userModel->updatePassword($idUsuario, $hash)) {
                $this->jsonResponse([
                    "success" => true,
                    "message" => "Contraseña restablecida correctamente.",
                    "tempPassword" => $nuevaPassword
                ]);
            } else {
                $this->jsonResponse(["success" => false, "message" => "Error al restablecer la contraseña."], 500);
            }
        }

        $this->jsonResponse(["success" => false, "message" => "Acción no válida."], 400);
    }

    /**
     * DELETE: Eliminar Usuario
     * Permiso: eliminar_usuario
     */
    public function delete()
    {
        checkAuth();

        if (!Permisos::tienePermiso('eliminar_usuario')) {
            $this->jsonResponse(["success" => false, "message" => "No tiene permiso para eliminar usuarios."], 403);
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $idUsuario = isset($input['id']) ? sanitizeInt($input['id']) : 0;

        $currentUser = currentUser();
        if ($currentUser && $idUsuario === $currentUser['id']) {
            $this->jsonResponse(["success" => false, "message" => "No puede eliminar su propio usuario."], 400);
        }

        if ($idUsuario <= 0) {
            $this->jsonResponse(["success" => false, "message" => "ID de usuario inválido."], 400);
        }

        if ($this->userModel->delete($idUsuario)) {
            $this->jsonResponse(["success" => true, "message" => "Usuario eliminado correctamente."]);
        } else {
            $this->jsonResponse(["success" => false, "message" => "Error al eliminar el usuario."], 500);
        }
    }

    // ====================================================================
    // HELPERS PRIVADOS
    // ====================================================================

    private function jsonResponse($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
