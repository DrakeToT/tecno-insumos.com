<!-- Modal: Asignar permisos -->
<div class="modal fade" id="modalRolePerms" tabindex="-1" aria-labelledby="modalRolePermsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalRolePermsLabel">
                    <i class="bi bi-shield-lock"></i> Permisos del Rol
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <h6 class="fw-bold">Rol seleccionado:</h6>
                <p id="nombreRolPerms" class="text-primary"></p>

                <hr>

                <h6 class="fw-bold">Permisos disponibles:</h6>
                <div id="contenedorPermisos" class="row g-3 mt-2">
                    <!-- Lista dinámica -->
                </div>
            </div>

            <div class="modal-footer">
                <button id="btnSavePerms" class="btn btn-success btn-sm">
                    <i class="bi bi-save"></i> Guardar permisos
                </button>
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
            </div>

        </div>
    </div>
</div>
