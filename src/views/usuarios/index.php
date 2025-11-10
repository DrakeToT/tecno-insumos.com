<?php
require_once __DIR__ . '/../../../src/helpers/session.php';
require_once __DIR__ . '/../../../src/config/base-url.php';

$titlePage = "Gestión de Usuarios - Tecno Insumos";
$extra_css = ["/assets/css/users.css"];
$extra_js  = ["/assets/js/users.js"];
$page = 'usuarios';
?>

<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>


<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-people-fill"></i> Gestión de Usuarios</h5>
            <button class="btn btn-success btn-sm" id="btnNewUser">
                <i class="bi bi-person-plus"></i> Nuevo Usuario
            </button>
        </div>

        <div class="card-body">
            <div class="mb-3 input-group ">
                <span class="input-group-text border-dark-subtle"><i class="bi bi-search"></i></span>
                <input type="text" id="buscarUsuario" class="border-dark-subtle focus-ring focus-ring-dark form-control" placeholder="Buscar por nombre, apellido o correo...">
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablaUsuarios">
                    <thead class="table-dark">
                        <tr>
                            <th class="sortable text-nowrap" data-sort="id">ID <i class="bi bi-caret-up-fill opacity-0"></i></th>
                            <th class="sortable text-nowrap" data-sort="nombre">Nombre <i class="bi bi-caret-up-fill opacity-0"></i></th>
                            <th class="sortable text-nowrap" data-sort="apellido">Apellido <i class="bi bi-caret-up-fill opacity-0"></i></th>
                            <th class="sortable text-nowrap" data-sort="email">Email <i class="bi bi-caret-up-fill opacity-0"></i></th>
                            <th class="sortable text-nowrap" data-sort="rol">Rol <i class="bi bi-caret-up-fill opacity-0"></i></th>
                            <th class="sortable text-nowrap" data-sort="estado">Estado <i class="bi bi-caret-up-fill opacity-0"></i></th>
                            <th class="text-center user-select-none text-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Se cargan dinámicamente por AJAX -->
                    </tbody>
                </table>
            </div>
            <nav id="paginationContainer" class="mt-3 d-flex justify-content-center"></nav>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/modals/modal-user-form.php';
require_once __DIR__ . '/../layouts/modals/modal-confirm.php';
require_once __DIR__ . '/../layouts/modals/modal-message.php';
require_once __DIR__ . '/../layouts/templates/template-user-row.php';

require_once __DIR__ . '/../layouts/footer.php';
?>