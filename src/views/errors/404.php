<?php require_once __DIR__ . '/../../config/base-url.php' ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light text-center d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="mb-4">
                    <i class="bi bi-sign-dead-end display-1 text-muted"></i>
                </div>
                <h1 class="display-1 fw-bold text-danger">404</h1>
                <h2 class="h4 mb-3">Página no encontrada</h2>
                <p class="lead text-muted mb-4">Lo sentimos, la página que estás buscando no existe o ha sido movida.</p>
                <a href="<?= BASE_URL; ?>" class="btn btn-primary px-4">
                    <i class="bi bi-arrow-left"></i> Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</body>
</html>