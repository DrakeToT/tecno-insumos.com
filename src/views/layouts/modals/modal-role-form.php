<!-- Modal: Crear / Editar Rol -->
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
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                    </div>
                    <div class="mb-3">
                        <label for="descripcionRol" class="form-label">Descripción</label>
                        <textarea id="descripcionRol" name="descripcion" class="form-control" rows="3"></textarea>
                        <div class="invalid-feedback">La descripción es obligatoria.</div>
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