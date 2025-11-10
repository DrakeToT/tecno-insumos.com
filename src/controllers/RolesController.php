<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/sanitize.php';

header('Content-Type: application/json; charset=utf-8');

// =========================
// Validar sesión
// =========================
if (!isUserLoggedIn()) {
    http_response_code(403);
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

try {
    $db = new Database();
    $conn = $db->getConnection();

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {

        // ======================================
        // GET → Listar todos los roles
        // ======================================
        case 'GET':
            $stmt = $conn->query("SELECT id, nombre, descripcion FROM roles ORDER BY nombre ASC");
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["success" => true, "roles" => $roles]);
            break;

        // ======================================
        // POST → Crear un nuevo rol
        // ======================================
        case 'POST':
            $input = json_decode(file_get_contents("php://input"), true);
            $nombre = sanitizeInput($input['nombre'] ?? '');
            $descripcion = sanitizeInput($input['descripcion'] ?? '');

            if ($nombre === '') {
                echo json_encode(["success" => false, "message" => "El nombre del rol es obligatorio."]);
                exit;
            }

            // Verificar duplicado
            $stmt = $conn->prepare("SELECT COUNT(*) FROM roles WHERE nombre = :nombre");
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(["success" => false, "message" => "Ya existe un rol con ese nombre."]);
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO roles (nombre, descripcion) VALUES (:nombre, :descripcion)");
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $success = $stmt->execute();

            echo json_encode([
                "success" => $success,
                "message" => $success ? "Rol creado correctamente." : "Error al crear el rol."
            ]);
            break;

        // ======================================
        // PUT → Editar un rol existente
        // ======================================
        case 'PUT':
            $input = json_decode(file_get_contents("php://input"), true);
            $id = (int) ($input['id'] ?? 0);
            $nombre = sanitizeInput($input['nombre'] ?? '');
            $descripcion = sanitizeInput($input['descripcion'] ?? '');

            if ($id <= 0 || $nombre === '') {
                echo json_encode(["success" => false, "message" => "Datos inválidos."]);
                exit;
            }

            // Verificar duplicado con otro rol
            $stmt = $conn->prepare("SELECT COUNT(*) FROM roles WHERE nombre = :nombre AND id != :id");
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                echo json_encode(["success" => false, "message" => "Ya existe otro rol con ese nombre."]);
                exit;
            }

            $stmt = $conn->prepare("UPDATE roles SET nombre = :nombre, descripcion = :descripcion WHERE id = :id");
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $success = $stmt->execute();

            echo json_encode([
                "success" => $success,
                "message" => $success ? "Rol actualizado correctamente." : "No se pudieron guardar los cambios."
            ]);
            break;

        // ======================================
        // DELETE → Eliminar un rol
        // ======================================
        case 'DELETE':
            $input = json_decode(file_get_contents("php://input"), true);
            $id = (int) ($input['id'] ?? 0);

            if ($id <= 0) {
                echo json_encode(["success" => false, "message" => "ID inválido."]);
                exit;
            }

            // Verificar si hay usuarios con ese rol antes de eliminarlo
            $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE idRol = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                echo json_encode([
                    "success" => false,
                    "message" => "No se puede eliminar el rol porque tiene usuarios asociados."
                ]);
                exit;
            }

            $stmt = $conn->prepare("DELETE FROM roles WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $success = $stmt->execute();

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
