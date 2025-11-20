<?php
require_once __DIR__ . '/../../../src/config/base-url.php';
require_once __DIR__ . '/../../../src/helpers/session.php';

// Validar sesión activa
if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/login');
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
            <button class="btn btn-success btn-sm" id="btnNewUser">
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
                            <th class="sortable" data-sort="id">ID <i class="bi bi-caret-up-fill text-warning"></i></th>
                            <th class="sortable" data-sort="nombre">Nombre <i class="bi bi-caret-up-fill opacity-0 text-warning"></i></th>
                            <th>Descripción</th>
                            <th class="sortable" data-sort="estado">Estado <i class="bi bi-caret-up-fill opacity-0 text-warning"></i></th>
                            <th class="text-center user-select-none text-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <!-- Cuerpo dinámico -->
                    </tbody>
                </table>
            </div>
            <nav id="paginationContainer" class="mt-3 d-flex justify-content-center"></nav>
        </div>
    </div>
</div>

<!-- =================== -->
<!-- Modal: Crear / Editar Rol -->
<!-- =================== -->
<div class="modal fade" id="modalRoleForm" tabindex="-1" aria-labelledby="modalRoleFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title text-capitalize" id="modalTitle">
                    <i class="bi bi-plus-circle" id="modalTitleIcon"></i> Nuevo Rol
                </h5>
                <button type="reset" form="formRol" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formRol" autocomplete="off" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombreRol" class="form-label">Nombre</label>
                        <input type="text" id="nombreRol" name="nombre" class="form-control" required>
                        <div class="invalid-feedback">Ingrese un nombre válido.</div>
                    </div>
                    <div class="mb-3">
                        <label for="descripcionRol" class="form-label">Descripción</label>
                        <textarea id="descripcionRol" name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                    <button type="reset" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Confirmar acción -->
<div class="modal fade" id="modalConfirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-question-circle"></i> Confirmar acción</h5>
            </div>
            <div class="modal-body text-center" id="modalConfirmMensaje"></div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="modalConfirmBtnAceptar">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Mensajes -->
<div class="modal fade" id="modalMessage" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header" id="modalMessageHeader">
                <h5 class="modal-title" id="modalMessageTitle"></h5>
            </div>
            <div class="modal-body text-center" id="modalMessageBody"></div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Template para filas -->
<template id="roleRowTemplate">
    <tr>
        <td class="id"></td>
        <td class="nombre"></td>
        <td class="descripcion"></td>
        <td>
            <button class="btn btn-outline-primary btn-sm btn-editar"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-outline-danger btn-sm btn-eliminar"><i class="bi bi-trash"></i></button>
        </td>
    </tr>
</template>

<!-- Template vacío -->
<template id="roleRowNullTemplate">
    <tr>
        <td colspan="4" class="text-muted py-3">No se encontraron roles.</td>
    </tr>
</template>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
