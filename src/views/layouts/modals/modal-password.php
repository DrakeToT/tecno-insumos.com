<div class="modal fade" id="modalPassword" tabindex="-1" aria-labelledby="modalPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalPasswordLabel">
                    <i class="bi bi-lock-fill"></i> Cambiar contraseña
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formPassword" novalidate>
                    <div class="mb-3">
                        <label for="actualPassword" class="form-label">Contraseña actual</label>
                        <input type="password" id="actualPassword" name="actualPassword" class="form-control" required>
                        <div class="invalid-feedback" id="feedbackActual"></div>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Nueva contraseña</label>
                        <input type="password" id="newPassword" name="newPassword"
                            class="form-control"
                            minlength="8"
                            pattern="(?=.*[A-Z])(?=.*\\d)[A-Za-z\\d@$!%*?&]{8,}" required>
                        <div class="invalid-feedback" id="feedbackNueva"></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                        <div class="invalid-feedback" id="feedbackConfirmar"></div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>