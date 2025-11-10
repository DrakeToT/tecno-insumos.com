<?php
$user = $_SESSION['user'] ?? null;

// Verificar si la foto existe físicamente
if (!empty($user['fotoPerfil'])) {
    $fotoArchivo = __DIR__ . '/../../public/assets/uploads/' . $user['fotoPerfil'];
    if (!file_exists($fotoArchivo)) {
        $user['fotoPerfil'] = null;
    }
}

// Si no tiene foto o se borró, usar imagen por defecto
$urlFoto = empty($user['fotoPerfil'])
    ? BASE_URL . '/assets/img/perfil.webp'
    : BASE_URL . '/assets/uploads/' . $user['fotoPerfil'];