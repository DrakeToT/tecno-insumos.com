<?php
$path = $_GET['action'] ?? '';

switch ($path) {
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
        echo json_encode(['error' => 'Endpoint no encontrado']);
}
