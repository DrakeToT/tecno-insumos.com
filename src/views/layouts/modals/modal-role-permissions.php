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
                <p class="ms-3"><span id="nombreRolPerms" class="badge bg-secondary fs-6"></span></p>

                <hr>

                <div class="row">
                    <div class="row row-cols-3">
                        <!-- Permisos disponibles -->
                        <div class="col-5">
                            <h6 class="fw-bold">Permisos disponibles:</h6>
                            <input type="text" id="searchDisponibles" class="form-control mb-2 border-dark-subtle focus-ring focus-ring-dark" placeholder="Buscar...">
                            <select id="listaDisponibles" class="form-select border-dark-subtle" size="10" multiple>
                                <!-- más permisos -->
                            </select>
                        </div>
    
                        <!-- Botones -->
                        <div class="col-2 d-flex flex-column justify-content-center align-items-center">
                            <button id="btnAsignar" class="btn btn-primary mb-2">→</button>
                            <button id="btnQuitar" class="btn btn-secondary">←</button>
                        </div>
    
                        <!-- Permisos asignados -->
                        <div class="col-5">
                            <h6 class="fw-bold">Permisos asignados:</h6>
                            <input type="text" id="searchAsignados" class="form-control mb-2 border-dark-subtle focus-ring focus-ring-dark" placeholder="Buscar...">
                            <select id="listaAsignados" class="form-select border-dark-subtle" size="10" multiple>
                                <!-- se llenará dinámicamente -->
                            </select>
                        </div>
                    </div>
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