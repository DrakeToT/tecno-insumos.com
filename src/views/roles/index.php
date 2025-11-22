<?php
require_once __DIR__ . '/../../../src/config/base-url.php';
require_once __DIR__ . '/../../../src/helpers/session.php';

// Validar sesión activa
if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL);
    exit;
}

$titlePage = "Gestión de Roles - Tecno Insumos";
$extra_js = ["/assets/js/roles.js"];
$page = 'roles';

require_once __DIR__ . '/../../views/layouts/header.php';
require_once __DIR__ . '/../../views/layouts/navbar.php';
?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-ui-radios"></i> Gestión de Roles</h5>
            <button class="btn btn-success btn-sm" id="btnNewRol">
                <i class="bi bi-plus-circle"></i> Nuevo Rol
            </button>
        </div>

        <div class="card-body">
            <div class="mb-3 input-group ">
                <span class="input-group-text border-dark-subtle"><i class="bi bi-search"></i></span>
                <input type="text" id="buscarRol" class="border-dark-subtle focus-ring focus-ring-dark form-control" placeholder="Buscar rol...">
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablaRoles">
                    <thead class="table-dark">
                        <tr>
                            <th class="sortable text-nowrap" data-sort="id">ID <i class="bi bi-caret-up-fill opacity-0 text-warning"></i></th>
                            <th class="sortable text-nowrap" data-sort="nombre">Nombre <i class="bi bi-caret-up-fill opacity-0 text-warning"></i></th>
                            <th class="user-select-none">Descripción</th>
                            <th class="sortable text-nowrap" data-sort="estado">Estado <i class="bi bi-caret-up-fill opacity-0 text-warning"></i></th>
                            <th class="text-center user-select-none text-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Cuerpo dinámico -->
                    </tbody>
                </table>
            </div>
            <nav id="paginationContainer" class="mt-3 d-flex justify-content-center"></nav>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/modals/modal-role-form.php';
require_once __DIR__ . '/../layouts/modals/modal-role-permissions.php';
require_once __DIR__ . '/../layouts/modals/modal-confirm.php';
require_once __DIR__ . '/../layouts/modals/modal-message.php';
require_once __DIR__ . '/../layouts/templates/template-role-row.php';

require_once __DIR__ . '/../../layouts/footer.php'; ?>