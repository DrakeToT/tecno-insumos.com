<?php
require_once __DIR__ . '/../../../src/helpers/session.php';
require_once __DIR__ . '/../../../src/config/base-url.php';

$user = currentUser();

if (!$user) {
    header('Location: ' . BASE_URL . '/home');
    exit;
}

// Datos dinámicos
$rolNombre = ucfirst($user['rol']['nombre'] ?? 'Usuario');
$nombreCompleto = trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''));
$titlePage = "Panel de $rolNombre - Tecno Insumos";
$page = 'inicio';

// Dependiendo del rol, podés cargar CSS o JS específicos (opcional)
$extra_css = ["/assets/css/perfil.css"];
$extra_js  = [];
?>

<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>

<?php require_once __DIR__ . '/../../views/layouts/navbar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col">
            <h1 class="display-6 text-center mb-4">
                Bienvenido<?php echo $nombreCompleto ? ', ' . htmlspecialchars($nombreCompleto) : ''; ?>
            </h1>

            <div class="card shadow-sm p-3 mb-4">
                <div class="card-body text-center">
                    <p class="lead">
                        Estás accediendo como <strong><?php echo htmlspecialchars($rolNombre); ?></strong>.
                    </p>
                    <p class="text-muted">
                        Fecha de alta: <?php echo date('d/m/Y', strtotime($user['fechaAlta'] ?? date('Y-m-d'))); ?>
                    </p>
                </div>
            </div>

            <!-- Sección de accesos rápidos según rol -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-3">

                <?php if ($user['rol']['nombre'] === 'Administrador'): ?>
                    <div class="col">
                        <a href="<?php echo BASE_URL; ?>/usuarios" class="card h-100 shadow-sm text-decoration-none text-dark">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-1 text-primary"></i>
                                <h5 class="mt-2">Gestión de Usuarios</h5>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="<?php echo BASE_URL; ?>/roles" class="card h-100 shadow-sm text-decoration-none text-dark">
                            <div class="card-body text-center">
                                <i class="bi bi-ui-radios fs-1 text-success"></i>
                                <h5 class="mt-2">Roles y Permisos</h5>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="<?php echo BASE_URL; ?>/configuracion" class="card h-100 shadow-sm text-decoration-none text-dark">
                            <div class="card-body text-center">
                                <i class="bi bi-gear fs-1 text-secondary"></i>
                                <h5 class="mt-2">Configuración</h5>
                            </div>
                        </a>
                    </div>

                <?php elseif ($user['rol']['nombre'] === 'Encargado de Stock'): ?>
                    <div class="col">
                        <a href="<?php echo BASE_URL; ?>/inventario" class="card h-100 shadow-sm text-decoration-none text-dark">
                            <div class="card-body text-center">
                                <i class="bi bi-box-seam fs-1 text-warning"></i>
                                <h5 class="mt-2">Gestión de Inventario</h5>
                            </div>
                        </a>
                    </div>

                <?php elseif ($user['rol']['nombre'] === 'Soporte Técnico'): ?>
                    <div class="col">
                        <a href="<?php echo BASE_URL; ?>/tickets" class="card h-100 shadow-sm text-decoration-none text-dark">
                            <div class="card-body text-center">
                                <i class="bi bi-tools fs-1 text-danger"></i>
                                <h5 class="mt-2">Casos de Soporte</h5>
                            </div>
                        </a>
                    </div>

                <?php elseif ($user['rol']['nombre'] === 'Coordinador IT'): ?>
                    <div class="col">
                        <a href="<?php echo BASE_URL; ?>/reportes" class="card h-100 shadow-sm text-decoration-none text-dark">
                            <div class="card-body text-center">
                                <i class="bi bi-graph-up fs-1 text-info"></i>
                                <h5 class="mt-2">Reportes Generales</h5>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Común a todos -->
                <div class="col">
                    <a href="<?php echo BASE_URL; ?>/perfil" class="card h-100 shadow-sm text-decoration-none text-dark">
                        <div class="card-body text-center">
                            <i class="bi bi-person-circle fs-1 text-dark"></i>
                            <h5 class="mt-2">Mi Perfil</h5>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>