<?php
// ====================================================================
// VISTA PRINCIPAL DE INVENTARIO (Equipos, Insumos, Consumibles)
// ====================================================================

require_once __DIR__ . '/../../../src/helpers/session.php';
require_once __DIR__ . '/../../../src/config/base-url.php';

$user = currentUser();

if (!$user) {
    header('Location: ' . BASE_URL . 'home');
    exit;
}

// Datos dinámicos para el Layout
$rolNombre = ucfirst($user['rol']['nombre'] ?? 'Usuario');
$nombreCompleto = trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''));
$titlePage = "Gestión de Inventario - Tecno Insumos";
$page = 'inventario';

// Scripts y CSS específicos para esta vista
$extra_css = [];
$extra_js  = ["/assets/js/inventario.js"];

?>

<!-- INICIO DEL LAYOUT -->
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <!-- Encabezado -->
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-box-seam"></i> Gestión de Inventario</h5>
        </div>

        <div class="card-body">
            <!-- PESTAÑAS DE NAVEGACIÓN -->
            <ul class="nav nav-tabs border-0" id="inventarioTabs" role="tablist" data-bs-theme="dark">
                <li class="nav-item" role="presentation">
                    <button class="btn btn-outline-dark rounded-bottom-0 active border-0" id="equipos-tab" data-bs-toggle="tab" data-bs-target="#equipos" type="button" role="tab" aria-controls="equipos" aria-selected="true">
                        <i class="bi bi-pc-display me-1"></i> Equipos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="btn btn-outline-dark rounded-bottom-0 border-0" id="insumos-tab" data-bs-toggle="tab" data-bs-target="#insumos" type="button" role="tab" aria-controls="insumos" aria-selected="false">
                        <i class="bi bi-tools me-1"></i> Insumos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="btn btn-outline-dark rounded-bottom-0 border-0" id="consumibles-tab" data-bs-toggle="tab" data-bs-target="#consumibles" type="button" role="tab" aria-controls="consumibles" aria-selected="false">
                        <i class="bi bi-droplet-half me-1"></i> Consumibles
                    </button>
                </li>
            </ul>

            <!-- CONTENIDO DE LAS PESTAÑAS -->
            <div class="tab-content" id="inventarioTabsContent">

                <!-- ======================================================= -->
                <!-- TAB 1: EQUIPOS INFORMÁTICOS -->
                <!-- ======================================================= -->
                <div class="tab-pane fade show active" id="equipos" role="tabpanel" aria-labelledby="equipos-tab">
                    <div class="card mb-4 rounded-end rounded-top-0">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <div class="fw-bold">Listado de Equipos Informáticos</div>
                            <button type="button" class="btn btn-success btn-sm" id="btnNewEquipo">
                                <i class="bi bi-plus-circle"></i> Nuevo Equipo
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Filtros Equipos -->
                            <div class="row mb-3">

                                <div class="input-group">
                                    <span class="input-group-text border-dark-subtle"><i class="bi bi-search"></i></span>
                                    <input type="text" id="buscarEquipo" class="border-dark-subtle focus-ring focus-ring-dark form-control" placeholder="Buscar por código, serie, marca...">
                                </div>

                            </div>

                            <!-- Tabla Equipos -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tablaEquipos" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="sortable text-nowrap" data-sort="codigo_inventario" role="button">Código <i class="bi bi-caret-up-fill opacity-0"></i></th>
                                            <th class="sortable text-nowrap" data-sort="categoria" role="button">Categoría <i class="bi bi-caret-up-fill opacity-0"></i></th>
                                            <th class="sortable text-nowrap" data-sort="marca" role="button">Marca/Modelo <i class="bi bi-caret-up-fill opacity-0"></i></th>
                                            <th class="user-select-none">Serie</th>
                                            <th class="sortable text-nowrap text-center" data-sort="estado" role="button">Estado <i class="bi bi-caret-up-fill opacity-0"></i></th>
                                            <th class="user-select-none">Ubicación</th>
                                            <th class="text-center user-select-none text-nowrap">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody><!-- JS: equipoRowTemplate --></tbody>
                                </table>
                            </div>

                            <!-- Paginación Equipos -->
                            <nav id="paginationEquipos" aria-label="Navegación equipos" class="mt-3 d-flex justify-content-center"></nav>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- TAB 2: INSUMOS (Placeholder para futuro desarrollo) -->
                <!-- ======================================================= -->
                <div class="tab-pane fade" id="insumos" role="tabpanel" aria-labelledby="insumos-tab">
                    <div class="card mb-4 rounded-end rounded-top-0">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <div class="fw-bold">Stock de Insumos</div>
                            <button type="button" class="btn btn-success btn-sm" onclick="alert('Próximamente: Módulo Insumos')">
                                <i class="bi bi-plus-circle"></i> Nuevo Insumo
                            </button>
                        </div>
                        <div class="card-body text-center py-5">
                            <i class="bi bi-tools display-4 text-muted"></i>
                            <h5 class="mt-3 text-muted">Módulo de Insumos en construcción</h5>
                            <p>Aquí podrás gestionar el stock de repuestos y componentes.</p>
                        </div>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- TAB 3: CONSUMIBLES (Placeholder para futuro desarrollo) -->
                <!-- ======================================================= -->
                <div class="tab-pane fade" id="consumibles" role="tabpanel" aria-labelledby="consumibles-tab">
                    <div class="card mb-4 rounded-end rounded-top-0">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <div class="fw-bold">Consumibles (Papel, Tintas, Etc)</div>
                            <button type="button" class="btn btn-success text-white btn-sm" onclick="alert('Próximamente: Módulo Consumibles')">
                                <i class="bi bi-plus-circle"></i> Nuevo Consumible
                            </button>
                        </div>
                        <div class="card-body text-center py-5">
                            <i class="bi bi-tools display-4 text-muted"></i>
                            <h5 class="mt-3 text-muted">Módulo de Consumibles en construcción</h5>
                            <p>Aquí podrás gestionar elementos de uso diario y rápido recambio.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- =================================================================== -->
<!-- INCLUDES FINALES -->
<!-- =================================================================== -->

<?php
require_once __DIR__ . '/../layouts/templates/template-equipo-row.php';
require_once __DIR__ . '/../layouts/modals/modal-equipo-form.php';
require_once __DIR__ . '/../layouts/modals/modal-confirm.php';
require_once __DIR__ . '/../layouts/modals/modal-message.php';

require_once __DIR__ . '/../layouts/footer.php';
?>