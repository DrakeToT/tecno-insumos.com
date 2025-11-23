<?php require_once __DIR__ . '/../../config/base-url.php' ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso Denegado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light text-center d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="mb-4">
                    <i class="bi bi-shield-lock-fill display-1 text-warning"></i>
                </div>
                <h1 class="display-1 fw-bold text-dark">403</h1>
                <h2 class="h4 mb-3">Acceso Restringido</h2>
                <p class="lead text-muted mb-4">
                    No tienes los permisos necesarios para acceder a esta sección. <br>
                    Si crees que esto es un error, contacta al Administrador.
                </p>
                <a href="<?= BASE_URL; ?>" class="btn btn-outline-dark px-4">
                    <i class="bi bi-house-door"></i> Ir al Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>