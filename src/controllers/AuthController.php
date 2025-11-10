<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/sanitize.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../config/base-url.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Verificar método
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método no permitido."]);
        exit;
    }

    // Decodificar cuerpo JSON
    $input = json_decode(file_get_contents("php://input"), true);
    $email = sanitizeInput($input['email'] ?? '');
    $password = sanitizeInput($input['password'] ?? '');

    // === Validaciones básicas ===
    if ($email === '' || $password === '') {
        echo json_encode(["success" => false, "message" => "Complete todos los campos."]);
        exit;
    }
    if (!validateEmail($email)) {
        echo json_encode(["success" => false, "message" => "Formato de correo electrónico inválido."]);
        exit;
    }
    if (!validateLength($password, 255, 8)) {
        echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres."]);
        exit;
    }

    // === Lógica de autenticación ===
    $userModel = new UserModel();
    $user = $userModel->findByEmail($email);

    if (!$user) {
        echo json_encode(["success" => false, "message" => "Credenciales inválidas."]);
        exit;
    }

    // Verificar estado
    if (strtolower($user['estado']) !== 'activo') {
        echo json_encode(["success" => false, "message" => "El usuario está inactivo."]);
        exit;
    }

    // Verificar contraseña
    if (!password_verify($password, $user['password'])) {
        echo json_encode(["success" => false, "message" => "Credenciales inválidas."]);
        exit;
    }

    // === Inicio de sesión exitoso ===
    $sessionData = buildUserSessionArray($user);
    setCurrentUser($sessionData);

    // Redirigir siempre al panel unificado
    $route = 'inicio';

    echo json_encode([
        "success"  => true,
        "message"  => "Inicio de sesión exitoso.",
        "redirect" => BASE_URL . '/' . $route
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error del servidor: Conexión fallida. Vuelva a intentar más tarde.", "error" => $e->getMessage()]);
    exit;
}
