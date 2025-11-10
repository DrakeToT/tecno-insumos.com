<!-- Modal: Crear / Editar Usuario -->
<div class="modal fade" id="modalUserForm" tabindex="-1" aria-labelledby="modalUserFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title text-capitalize" id="modalTitle">
                    <i class="bi bi-person-plus-fill" id="modalTitleIcon"></i>
                </h5>
                <button type="reset" form="formUsuario" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formUsuario" autocomplete="off" novalidate>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombreUsuario" class="form-label">Nombre</label>
                            <input type="text" id="nombreUsuario" name="nombre" class="form-control" required>
                            <div class="invalid-feedback">Ingrese el nombre del usuario.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="apellidoUsuario" class="form-label">Apellido</label>
                            <input type="text" id="apellidoUsuario" name="apellido" class="form-control" required>
                            <div class="invalid-feedback">Ingrese el apellido del usuario.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="emailUsuario" class="form-label">Correo electrónico</label>
                            <input type="email" id="emailUsuario" name="email" class="form-control" autocomplete="off" required>
                            <div class="invalid-feedback">Ingrese un correo electrónico válido.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="rolUsuario" class="form-label">Rol</label>
                            <select id="rolUsuario" name="rol" class="form-select" required>
                                <option value="" selected disabled>Seleccione un rol</option>
                                <option value="1">Administrador</option>
                                <option value="2">Encargado de Stock</option>
                                <option value="3">Soporte Técnico</option>
                                <option value="4">Coordinador IT</option>
                            </select>
                            <div class="invalid-feedback">Seleccione un rol para el usuario.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="estadoUsuario" class="form-label">Estado</label>
                            <select id="estadoUsuario" name="estado" class="form-select" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                            <div class="invalid-feedback">Seleccione un estado válido.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="passwordUsuario" class="form-label">Contraseña</label>
                            <input type="password" id="passwordUsuario" name="password" class="form-control" autocomplete="new-password" required>
                            <div class="invalid-feedback">Ingrese una contraseña válida.</div>
                        </div>
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