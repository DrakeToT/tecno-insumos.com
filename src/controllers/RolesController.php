<?php
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/PermisoModel.php';
require_once __DIR__ . '/../helpers/sanitize.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/permisos.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar sesión
if (!isUserLoggedIn()) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Acceso no autorizado."]);
    exit;
}

$user = currentUser();
$idUsuario = $user['id'];
$idRolUsuario = $user['rol']['id'] ?? null;

// Restringir acceso solo a Administrador
if ((int)$idRolUsuario !== 1) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "No tiene permisos para acceder a Roles."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$roleModel = new RoleModel();

try {
    switch ($method) {

        /**
         * GET 
         * -> Listar roles con búsqueda, ordanamiento y paginación.
         * -> Obtiene un rol y sus permisos asignados. 
         */
        case 'GET':
            if (!Permisos::tienePermiso('ver_roles')) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para ver roles."]);
                exit;
            }

            // Si se envía un ID devuelve solo ese rol
            if (isset($_GET['id'])) {

                $idRol = sanitizeInt($_GET['id'] ?? 0); // Sanitizo ID
                if ($idRol <= 0 || $idRol === false) {
                    echo json_encode(["success" => false, "message" => "Formato de ID inválido. Debe ser un número entero."]);
                    exit;
                }

                $permisoModel = new PermisoModel();

                $rol = $roleModel->findById($idRol);
                $permisosDisponibles = $permisoModel->getAll(); // Obtengo todos los permisos de la DB
                $permisosAsignados = $permisoModel->getByRol($idRol); // Obtengo todos los permisos asignados a un rol específico

                // Transformo el campo "nombre"
                $permisosDisponibles = array_map(function ($permiso) {
                    // Reemplazo "_" por espacio
                    $permiso['nombre'] = str_replace('_', ' ', $permiso['nombre']);
                    // Capitalizo cada palabra
                    $permiso['nombre'] = ucwords($permiso['nombre']);
                    return $permiso;
                }, $permisosDisponibles);

                echo json_encode([
                    "success" => true,
                    "rol" => $rol,
                    "permisosDisponibles" => $permisosDisponibles,
                    "permisosAsignados" => array_column($permisosAsignados, 'id')
                ]);
                exit;
            }

            // Si no hay ID, listar roles con búsqueda y ordenamiento
            $search = trim($_GET['search'] ?? "");
            $sort   = $_GET['sort'] ?? "id";
            $order  = $_GET['order'] ?? "ASC";
            $limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Validar sort y order para evitar SQL injection
            $allowedSort = ["id", "nombre", "descripcion", "estado"];
            if (!in_array($sort, $allowedSort)) {
                $sort = "id";
            }
            $order = $order === "DESC" ? "DESC" : "ASC";

            // Obtener roles filtrados
            $roles = $roleModel->getAll($search, $sort, $order, $limit, $offset);

            // Obtener total de registros para calcular páginas
            $total = $roleModel->countAll($search);

            echo json_encode([
                "success" => true,
                "roles"   => $roles,
                "pagination" => [
                    "total" => $total,
                    "page" => $page,
                    "limit" => $limit,
                    "pages" => ceil($total / $limit)
                ]
            ]);
            break;

        /**
         * POST -> Crear rol
         */
        case 'POST':
            if (!Permisos::tienePermiso('crear_roles')) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para crear roles."]);
                exit;
            }

            // Detectar Content-Type
            $contentType = $_SERVER["CONTENT_TYPE"] ?? "";

            if (stripos($contentType, "application/json") !== false) {
                // JSON
                $input = json_decode(file_get_contents("php://input"), true) ?? [];
            } elseif (stripos($contentType, "multipart/form-data") !== false) {
                // FormData (se recibe como $_POST)
                $input = $_POST;
            } else {
                echo json_encode(["success" => false, "message" => "Formato de datos no soportado."]);
                exit;
            }

            // Extraer y sanitizar
            $nombre = sanitizeInput($input['nombre'] ?? '');
            $descripcion = sanitizeInput($input['descripcion'] ?? '');

            // Validaciones
            $errors = [];

            if ($nombre === '' || !validateLength($nombre, 50, 3)) {
                $errors['nombre'] = "El nombre debe tener entre 3 y 50 caracteres.";
            } elseif (!validateLetters($nombre)) {
                $errors['nombre'] = "El nombre solo puede contener letras y espacios.";
            }

            if ($descripcion === '' && !validateLength($descripcion, 255, 5)) {
                $errors['descripcion'] = "La descripción debe tener entre 5 y 255 caracteres.";
            }elseif (!validateLetters($descripcion)){
                $errors['descripcion'] = "La descripción solo puede contener letras y espacios.";
            }

            if (!empty($errors)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Errores de validación.",
                    "errors" => $errors
                ]);
                exit;
            }

            // Crear Rol
            $success = $roleModel->create($nombre, $descripcion);

            echo json_encode([
                "success" => $success,
                "message" => $success ? "Rol creado correctamente." : "Error al crear el rol."
            ]);
            break;

        /**
         * PUT -> Actualizar rol existente
         */
        case 'PUT':
            if (!Permisos::tienePermiso('editar_roles')) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para editar roles."]);
                exit;
            }

            // Detectar Content-Type
            $contentType = $_SERVER["CONTENT_TYPE"] ?? "";

            if (stripos($contentType, "application/json") !== false) {
                // JSON
                $input = json_decode(file_get_contents("php://input"), true) ?? [];
            } elseif (stripos($contentType, "multipart/form-data") !== false) {
                // FormData (se recibe como $_POST)
                $input = $_POST;
            } else {
                echo json_encode(["success" => false, "message" => "Formato de datos no soportado."]);
                exit;
            }

            // Extraer y sanitizar
            $id = sanitizeInt($input['id'] ?? 0);
            $nombre = sanitizeInput($input['nombre'] ?? '');
            $descripcion = sanitizeInput($input['descripcion'] ?? '');
            $estado = sanitizeInput($input['estado'] ?? '');

            // Validaciones
            $errors = [];

            if ($id <= 0 || !$roleModel->findById($id)) {
                $errors['id'] = "El rol especificado no existe.";
            }

            if ($nombre === '' || !validateLength($nombre, 50, 3)) {
                $errors['nombre'] = "El nombre debe tener entre 3 y 50 caracteres.";
            } elseif (!validateAlphanumeric($nombre, true)) {
                $errors['nombre'] = "El nombre solo puede contener letras, números y espacios.";
            }

            if ($descripcion === '' && !validateLength($descripcion, 255, 5)) {
                $errors['descripcion'] = "La descripción debe tener entre 5 y 255 caracteres.";
            }elseif (!validateLetters($descripcion)){
                $errors['descripcion'] = "La descripción solo puede contener letras y espacios.";
            }

            if (!in_array($estado, ['Activo', 'Inactivo'])) {
                $errors['estado'] = "Estado inválido.";
            }

            if (!empty($errors)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Errores de validación.",
                    "errors" => $errors
                ]);
                exit;
            }

            // Actualizar
            $success = $roleModel->update($id, $nombre, $descripcion, $estado);

            echo json_encode([
                "success" => $success,
                "message" => $success ? "Rol actualizado correctamente." : "No se pudo actualizar el rol."
            ]);
            break;

        /**
         * PATCH -> asignar permisos a un rol 
         *  */
        case 'PATCH':
            if (!Permisos::tienePermiso('asignar_permisos')) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para asignar permisos."]);
                exit;
            }

            $input = json_decode(file_get_contents("php://input"), true);

            if (!isset($input['idRol'])  || empty($input['idRol'])) {
                echo json_encode(["success" => false, "message" => "ID del rol es obligatorio."]);
                exit;
            }

            $idRol = sanitizeInt($input['idRol'] ?? 0);
            $permisosSeleccionados = $input['permisos'] ?? [];

            if ($idRol <= 0 || $idRol === false) {
                echo json_encode(["success" => false, "message" => "ID de rol inválido."]);
                exit;
            }

            if (!is_array($permisosSeleccionados)) {
                echo json_encode(["success" => false, "message" => "Los permisos deben enviarse como un array."]);
                exit;
            }

            $permisoModel = new PermisoModel();
            // borrar permisos existentes
            $permisoModel->clearByRol($idRol);

            // asignar permisos nuevos si los hay
            if (!empty($permisosSeleccionados)) {
                $permisoModel->assign($idRol, $permisosSeleccionados);
            }

            echo json_encode([
                "success" => true,
                "message" => "Permisos actualizados correctamente."
            ]);
            break;


        /**
         * DELETE -> Eliminar rol
         */
        case 'DELETE':
            if (!Permisos::tienePermiso('eliminar_roles')) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para eliminar roles."]);
                exit;
            }

            $input = json_decode(file_get_contents("php://input"), true);
            $id = (int)($input['id'] ?? 0);

            if ($id <= 0) {
                echo json_encode(["success" => false, "message" => "ID de rol inválido."]);
                exit;
            }

            if ($roleModel->hasUsers($id)) {
                echo json_encode(["success" => false, "message" => "No se puede eliminar un rol con usuarios asignados."]);
                exit;
            }

            $success = $roleModel->delete($id);

            echo json_encode([
                "success" => $success,
                "message" => $success ? "Rol eliminado correctamente." : "Error al eliminar el rol."
            ]);
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
        "message" => "Error del servidor: " . $e->getMessage()
    ]);
}
