<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si hay una sesión activa.
 * Retorna true si hay usuario autenticado, false en caso contrario.
 */
function isUserLoggedIn(): bool {
    return isset($_SESSION['user']);
}

/**
 * Devuelve el usuario logueado, o null si no hay sesión.
 */
function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Cierra la sesión actual de forma segura.
 */
function logout(): void {
    if (session_status() !== PHP_SESSION_NONE) {
        session_unset();
        session_destroy();
    }
}

/**
 * Construye un array de sesión del usuario a partir de un registro de la DB.
 * Esto garantiza un formato consistente en toda la aplicación.
 */
function buildUserSessionArray(array $userDb): array {
    return [
        'id'         => $userDb['id'],
        'nombre'     => $userDb['nombre'],
        'apellido'   => $userDb['apellido'],
        'email'      => $userDb['email'],
        'fotoPerfil' => $userDb['fotoPerfil'] ?? null,
        'rol'        => [
            'id'     => $userDb['idRol'] ?? null,
            'nombre' => $userDb['rol'] ?? null
        ],
        'fechaAlta'  => $userDb['fechaAlta'] ?? null,
        'estado'     => $userDb['estado'] ?? null
    ];
}

/**
 * Refresca la información del usuario en sesión con los datos actuales desde la base de datos.
 * Devuelve el nuevo array de sesión o null si el usuario no existe.
 */
function refreshUserSession(int $idUsuario): ?array {
    require_once __DIR__ . '/../models/UserModel.php';
    $userModel = new UserModel();

    $userDb = $userModel->findById($idUsuario);
    if ($userDb) {
        $_SESSION['user'] = buildUserSessionArray($userDb);
    }

    return $_SESSION['user'] ?? null;
}

/**
 * Establece el usuario en la sesión a partir de un array ya estructurado.
 */
function setCurrentUser(array $user): void {
    $_SESSION['user'] = $user;
}
