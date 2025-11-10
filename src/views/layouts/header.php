<?php
if (!isset($titlePage)) {
    $titlePage = "Sistema de Inventario - Tecno Insumos";
}

// Arrays opcionales para estilos y scripts adicionales
$extra_css = $extra_css ?? [];
$extra_js  = $extra_js ?? [];

require_once __DIR__ . '/../../../src/config/base-url.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo htmlspecialchars($titlePage); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">

    <?php foreach ($extra_css as $css): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL . $css; ?>">
    <?php endforeach; ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?php echo BASE_URL . $js; ?>" defer></script>
    <?php endforeach; ?>
</head>

<body class="bg-body-secondary pe-0">