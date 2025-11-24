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
            isset($_GET['id']) ? $controller->getOne() : $controller->getAll();
            break;
        case 'POST':
            $controller->create();
            break;
        case 'PUT':
            $controller->update();
            break;
        case 'DELETE':
            $controller->delete();
            break;
        default:
            sendMethodNotAllowed();
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
    if ($method === 'GET') $controller->getCategorias();
    else sendMethodNotAllowed();
    exit;
}

// =============================================================
// RECURSO: USUARIOS (?users)
// =============================================================
if (isset($_GET['users'])) {
    require_once __DIR__ . '/../../src/controllers/UsersController.php';
    $user = new UsersController();

    switch ($method) {
        case 'GET':
            isset($_GET['id']) ? $user->getOne() : $user->getAll();
            break;
        case 'POST':
            $user->create();
            break;
        case 'PUT':
            $user->update();
            break;
        case 'PATCH':
            $user->changeStatusOrPassword();
            break;
        case 'DELETE':
            $user->delete();
            break;
        default:
            sendMethodNotAllowed();
            break;
    }
}

// =============================================================
// RECURSO: ROLES (?roles)
// =============================================================
if (isset($_GET['roles'])) {
    require_once __DIR__ . '/../../src/controllers/RolesController.php';
    $roles = new RolesController();
    switch ($method) {
        case 'GET':
            isset($_GET['id']) ? $roles->getOne() : $roles->getAll();
            break;
        case 'POST':
            $roles->create();
            break;
        case 'PUT':
            $roles->update();
            break;
        case 'PATCH':
            $roles->updatePermissions();
            break;
        case 'DELETE':
            $roles->delete();
            break;
        default:
            sendMethodNotAllowed();
            break;
    }
    exit;
}

// =============================================================
// RECURSO: PERFIL (?perfil)
// =============================================================
if (isset($_GET['perfil'])) {
    require_once __DIR__ . '/../../src/controllers/ProfileController.php';
    $controller = new ProfileController();
    switch ($method) {
        case 'GET':   $controller->getProfile(); break;     // Obtener datos
        case 'PUT':   $controller->updateData(); break;     // Editar datos
        case 'PATCH': $controller->changePassword(); break; // Cambiar password
        case 'POST':  $controller->uploadPhoto(); break;    // Subir foto
        default: sendMethodNotAllowed(); break;
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

// Helper
function sendMethodNotAllowed()
{
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
