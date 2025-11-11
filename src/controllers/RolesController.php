<?php
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../helpers/sanitize.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/permisos.php';

header('Content-Type: application/json; charset=utf-8');

// =========================
// Validar sesión
// =========================
if (!isUserLoggedIn()) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Acceso no autorizado."]);
    exit;
}

$user = currentUser();
$idRol = $user['rol']['id'] ?? null;

// Por ahora sólo el administrador (rol 1) puede gestionar roles
if ((int)$idRol !== 1) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "No tiene permisos para acceder a Roles."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$roleModel = new RoleModel();

try {
    
    switch ($method) {

        // ======================================
        // GET → Listar todos los roles
        // ======================================
        case 'GET':
            if (!Permisos::tienePermiso('ver_roles', $idUsuario)) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para ver roles."]);
                exit;
            }
            $roles = $roleModel->getAll();
            echo json_encode(["success" => true, "roles" => $roles]);
            break;

        // ======================================
        // POST → Crear un nuevo rol
        // ======================================
        case 'POST':
            if (!Permisos::tienePermiso('crear_roles', $idUsuario)) {
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

            // NOTA: Verificar duplicado
            
            $success = $roleModel->create($nombre, $descripcion);
            echo json_encode([
                "success" => $success,
                "message" => $success ? "Rol creado correctamente." : "Error al crear el rol."
            ]);
            break;

        // ======================================
        // PUT → Editar un rol existente
        // ======================================
        case 'PUT':
            if (!Permisos::tienePermiso('editar_roles', $idUsuario)) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para editar roles."]);
                exit;
            }

            $input = json_decode(file_get_contents("php://input"), true);
            $id = (int) ($input['id'] ?? 0);
            $nombre = sanitizeInput($input['nombre'] ?? '');
            $descripcion = sanitizeInput($input['descripcion'] ?? '');

            if ($id <= 0 || $nombre === '') {
                echo json_encode(["success" => false, "message" => "Datos incompletos para actualizar."]);
                exit;
            }

            // NOTA: Verificar duplicado con otro rol
            
            $success = $roleModel->update($id, $nombre, $descripcion);
            echo json_encode([
                "success" => $success,
                "message" => $success ? "Rol actualizado correctamente." : "No se pudo actualizar el rol."
            ]);
            break;

        // ======================================
        // DELETE → Eliminar un rol
        // ======================================
        case 'DELETE':
            if (!Permisos::tienePermiso('eliminar_roles', $idUsuario)) {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "No tiene permiso para eliminar roles."]);
                exit;
            }

            $input = json_decode(file_get_contents("php://input"), true);
            $id = (int) ($input['id'] ?? 0);

            if ($id <= 0) {
                echo json_encode(["success" => false, "message" => "ID de rol inválido."]);
                exit;
            }

            // Evita eliminar roles con usuarios asignados
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
    echo json_encode(["success" => false, "message" => "Error del servidor: " . $e->getMessage()]);
}
