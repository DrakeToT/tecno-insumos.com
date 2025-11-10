<?php
require_once __DIR__ . '/../../../src/config/base-url.php';
require_once __DIR__ . '/../../../src/helpers/session.php';

logout();

// Redirigir al home público
header('Location: ' . BASE_URL . '/home');
exit;
