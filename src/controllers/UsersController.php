<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/sanitize.php';
require_once __DIR__ . '/../helpers/session.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Verificar sesión y permisos (solo admin debería acceder)
    if (!isUserLoggedIn()) {
        echo json_encode(["success" => false, "message" => "Acceso no autorizado."]);
        http_response_code(403);
        exit;
    }

    $user = currentUser();
    $idRol = $user['rol']['id'] ?? null;

    // Validar que el rol sea Administrador (idRol = 1)
    if ($idRol !== 1) {
        echo json_encode(["success" => false, "message" => "No tiene permisos para acceder a esta sección."]);
        http_response_code(403);
        exit;
    }

    $userModel = new UserModel();
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {

        // =====================================================
        // GET → Listar usuarios (con búsqueda opcional) u obtener uno solo por ID
        // =====================================================
        case 'GET':
            $userModel = new UserModel();

            // Si se envía un ID, devuelve solo ese usuario
            if (isset($_GET['id'])) {
                $idUsuario = (int) $_GET['id'];
                $usuario = $userModel->findById($idUsuario);

                if ($usuario) {
                    echo json_encode(["success" => true, "usuario" => $usuario]);
                } else {
                    echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
                }
                break;
            }

            // Si no hay ID, lista los usuarios
            // Parámetros recibidos
            $search = trim($_GET['search'] ?? '');
            $sort = $_GET['sort'] ?? 'id';
            $order = $_GET['order'] ?? 'ASC';
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = intval($_GET['limit'] ?? 10);
            $offset = ($page - 1) * $limit;

            // Validar parámetros
            $allowedSort = ['id', 'nombre', 'apellido', 'email', 'estado', 'rol'];
            if (!in_array($sort, $allowedSort)) $sort = 'id';

            $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

            // Obtener usuarios y total
            $usuarios = $userModel->getAll($search, $sort, $order, $limit, $offset);
            $totalUsuarios = $userModel->countAll($search);
            $totalPaginas = ceil($totalUsuarios / $limit);

            echo json_encode([
                "success" => true,
                "usuarios" => $usuarios,
                "pagination" => [
                    "currentPage" => $page,
                    "totalPages" => $totalPaginas,
                    "totalItems" => $totalUsuarios,
                    "perPage" => $limit
                ]
            ]);

            break;

        // =====================================================
        // POST → Crear un nuevo usuario
        // =====================================================
        case 'POST':
            $input = json_decode(file_get_contents("php://input"), true);

            $nombre = sanitizeInput($input['nombre'] ?? '');
            $apellido = sanitizeInput($input['apellido'] ?? '');
            $email = sanitizeInput($input['email'] ?? '');
            $password = sanitizeInput($input['password'] ?? '');
            $idRol = (int) ($input['rol'] ?? 0);
            $estado = sanitizeInput($input['estado'] ?? 'Activo');

            // Validaciones básicas
            if ($nombre === '' || $apellido === '' || $email === '' || $password === '' || !$idRol) {
                echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
                exit;
            }
            // Valida el formato de email
            if (!validateEmail($email)) {
                echo json_encode(["success" => false, "message" => "Correo electrónico inválido."]);
                exit;
            }
            // Valida cantidad de caracteres
            if (!validateLength($password, 255, 8)) {
                echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres."]);
                exit;
            }
            // Verifica si el email ya existe
            if ($userModel->findByEmail($email)) {
                echo json_encode(["success" => false, "message" => "El correo electrónico ya está registrado."]);
                exit;
            }

            // Hashear contraseña antes de guardar
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $data = [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'password' => $hashedPassword,
                'idRol' => $idRol,
                'estado' => $estado
            ];

            $success = $userModel->create($data);

            echo json_encode([
                "success" => $success,
                "message" => $success
                    ? "Usuario creado correctamente."
                    : "Error al crear el usuario."
            ]);
            break;

        // =====================================================
        // PUT → Editar usuario existente
        // =====================================================
        case 'PUT':
            $input = json_decode(file_get_contents("php://input"), true);

            $idUsuario = (int) ($input['id'] ?? 0);
            $nombre   = sanitizeInput($input['nombre'] ?? '');
            $apellido = sanitizeInput($input['apellido'] ?? '');
            $email    = sanitizeInput($input['email'] ?? '');
            $rol      = (int) ($input['rol'] ?? 0);
            $estado   = sanitizeInput($input['estado'] ?? 'Activo');

            if ($idUsuario <= 0 || $nombre === '' || $apellido === '' || $email === '' || $rol === 0) {
                echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
                exit;
            }

            if (!validateEmail($email)) {
                echo json_encode(["success" => false, "message" => "Correo electrónico inválido."]);
                exit;
            }

            // Verificar si el email ya pertenece a otro usuario
            $existingUser = $userModel->findByEmail($email);
            if ($existingUser && (int)$existingUser['id'] !== $idUsuario) {
                echo json_encode(["success" => false, "message" => "El correo electrónico ya pertenece a otro usuario."]);
                exit;
            }

            $data = [
                'nombre'   => $nombre,
                'apellido' => $apellido,
                'email'    => $email,
                'idRol'    => $rol,
                'estado'   => $estado
            ];

            $success = $userModel->update($idUsuario, $data);

            $response = [
                "success" => $success,
                "message" => $success
                    ? "Usuario actualizado correctamente."
                    : "No se pudieron guardar los cambios."
            ];

            // Actualiza la sesión si el usuario editado es el que está logueado
            if ($success && $idUsuario === $user['id']) {
                refreshUserSession($idUsuario);
                $response["redirect"] = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
            }

            echo json_encode($response);
            break;

        // =====================================================
        // PATCH → Cambiar estado de usuario (Activo/Inactivo)
        // =====================================================
        case 'PATCH':
            $input = json_decode(file_get_contents("php://input"), true);
            $idUsuario = (int) ($input['id'] ?? 0);
            $accion = sanitizeInput($input['accion'] ?? '');

            if (!$idUsuario || $accion === '') {
                echo json_encode(["success" => false, "message" => "Datos incompletos."]);
                exit;
            }

            // Cambiar estado (Activo/Inactivo)
            if ($accion === 'estado') {

                // Evitar cambiar el propio estado
                if ($idUsuario === $user['id']) {
                    echo json_encode(["success" => false, "message" => "No puede cambiar el estado de su propio usuario."]);
                    exit;
                }

                $nuevoEstado = sanitizeInput($input['estado'] ?? '');
                if (!in_array($nuevoEstado, ['Activo', 'Inactivo'])) {
                    echo json_encode(["success" => false, "message" => "Estado inválido."]);
                    exit;
                }

                $success = $userModel->changeStatus($idUsuario, $nuevoEstado);
                echo json_encode([
                    "success" => $success,
                    "message" => $success
                        ? "Estado actualizado correctamente."
                        : "Error al actualizar el estado."
                ]);
                exit;
            }

            // Restablecer contraseña temporal
            if ($accion === 'reset-password') {
                $nuevaPassword = 'TempPass' . rand(1000, 9999);
                $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

                $success = $userModel->updatePassword($idUsuario, $hash);
                echo json_encode([
                    "success" => $success,
                    "message" => $success
                        ? "Contraseña restablecida correctamente."
                        : "Error al restablecer la contraseña.",
                    "tempPassword" => $success ? $nuevaPassword : null
                ]);
                exit;
            }

            echo json_encode(["success" => false, "message" => "Acción no válida."]);
            break;

        // =====================================================
        // DELETE → Eliminar usuario
        // =====================================================
        case 'DELETE':
            $input = json_decode(file_get_contents("php://input"), true);
            $idUsuario = (int) ($input['id'] ?? 0);

            //  Evitar eliminarse a sí mismo
            if ($idUsuario === $user['id']) {
                echo json_encode(["success" => false, "message" => "No puede eliminar su propio usuario."]);
                exit;
            }

            if (!$idUsuario) {
                echo json_encode(["success" => false, "message" => "ID de usuario inválido."]);
                exit;
            }

            if ($userModel->delete($idUsuario)) {
                echo json_encode(["success" => true, "message" => "Usuario eliminado correctamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al eliminar el usuario."]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Método no permitido."]);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error de servidor: " . $e->getMessage()
    ]);
}
