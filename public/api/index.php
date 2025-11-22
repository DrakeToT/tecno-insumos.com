<?php
require_once __DIR__ . '/../../src/config/base-url.php';

$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

// =============================================================
// RECURSO: EQUIPOS (?equipos)
// =============================================================
if (isset($_GET['equipos'])) {
    require_once __DIR__ . '/../../src/controllers/EquiposController.php';
    $controller = new EquiposController();

    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $controller->getOne();    // Obtener uno
            } else {
                $controller->getAll();    // Listar todos
            }
            break;

        case 'POST':
            $controller->create();        // Crear
            break;

        case 'PUT':
            $controller->update();        // Actualizar (Reemplazo/Edición)
            break;

        case 'DELETE':
            $controller->delete();        // Eliminar
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
    exit;
}

// =============================================================
// RECURSO: CATEGORÍAS (?categorias)
// =============================================================
if (isset($_GET['categorias'])) {
    require_once __DIR__ . '/../../src/controllers/EquiposController.php';
    $controller = new EquiposController();

    if ($method === 'GET') {
        $controller->getCategorias();
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    exit;
}

// =============================================================
// COMPATIBILIDAD LEGACY (Usuarios/Roles/Auth siguen con ?action=)
// =============================================================
$action = $_GET['action'] ?? '';

if ($action) {
    switch ($action) {
        case 'login':
            require_once __DIR__ . '/../../src/controllers/AuthController.php';
            break;
        case 'perfil':
            require_once __DIR__ . '/../../src/controllers/ProfileController.php';
            break;
        case 'users':
            require_once __DIR__ . '/../../src/controllers/UsersController.php';
            break;
        case 'roles':
            require_once __DIR__ . '/../../src/controllers/RolesController.php';
            break;
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Endpoint no encontrado']);
            break;
    }
} else {
    // Si no hay ni recurso (?equipos) ni acción (?action)
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}
