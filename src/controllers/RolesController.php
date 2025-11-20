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
         * GET → Listar roles, obtener rol + permisos asignados
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

                echo json_encode([
                    "success" => true,
                    "rol" => $rol,
                    "permisosDisponibles" => $permisosDisponibles,
                    "permisosAsignados" => array_column($permisosAsignados, 'id')
                ]);
                exit;
            }

            $roles = $roleModel->getAll();
            echo json_encode(["success" => true, "roles" => $roles]);
            break;

        /**
         * POST → Crear rol
         */
        case 'POST':
            if (!Permisos::tienePermiso('crear_roles')) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para crear roles."]);
                exit;
            }

            $input = json_decode(file_get_contents("php://input"), true);

            $nombre = sanitizeInput($input['nombre'] ?? '');
            $descripcion = sanitizeInput($input['descripcion'] ?? '');

            if ($nombre === '') {
                echo json_encode(["success" => false, "message" => "El nombre del rol es obligatorio."]);
                exit;
            }

            $success = $roleModel->create($nombre, $descripcion);

            echo json_encode([
                "success" => $success,
                "message" => $success ? "Rol creado correctamente." : "Error al crear el rol."
            ]);
            break;

        /**
         * PUT → Actualizar rol existente
         */
        case 'PUT':
            if (!Permisos::tienePermiso('editar_roles')) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para editar roles."]);
                exit;
            }

            $input = json_decode(file_get_contents("php://input"), true);

            $id = (int)($input['id'] ?? 0);
            $nombre = sanitizeInput($input['nombre'] ?? '');
            $descripcion = sanitizeInput($input['descripcion'] ?? '');
            $estado = sanitizeInput($input['estado'] ?? '');

            if ($id <= 0 || $nombre === '') {
                echo json_encode(["success" => false, "message" => "Datos incompletos para actualizar."]);
                exit;
            }

            if (!in_array($estado, ['Activo', 'Inactivo'])) {
                echo json_encode(["success" => false, "message" => "Estado inválido."]);
                exit;
            }

            $success = $roleModel->update($id, $nombre, $descripcion, $estado);

            echo json_encode([
                "success" => $success,
                "message" => $success ? "Rol actualizado correctamente." : "No se pudo actualizar el rol."
            ]);
            break;

        /**
         * PATCH → asignar permisos a un rol 
         *  */ 
        case 'PATCH':
            if (!Permisos::tienePermiso('asignar_permisos')) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para asignar permisos."]);
                exit;
            }

            $input = json_decode(file_get_contents("php://input"), true);
            
            if(!isset($input['idRol'])  || empty($input['idRol']) ){
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
         * DELETE → Eliminar rol
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
