<!-- Modal -->
<div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark bg-gradient text-white shadow">
                <h5 class="modal-title" id="modalLoginLabel">Iniciar Sesión</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" class="text-center mt-3" id="formLogIn" method="POST" novalidate>
                    <div class="input-group mb-3 row-cols-2">
                        <span class="input-group-text col-2 p-0 position-relative" id="" for="loginUsername">
                            <i class="bi bi-person-fill fs-3 position-absolute top-50 start-50 translate-middle"></i>
                        </span>
                        <div class="form-floating col-10">
                            <input type="email" name="email" id="loginEmail" class="form-control rounded-0 rounded-end" autocomplete="email"
                                placeholder="name@example.com" aria-label="Username" aria-describedby="" required>
                            <label for="loginEmail">Correo electrónico</label>
                        </div>
                    </div>
                    <div class="input-group mb-3 row-cols-2">
                        <span id="" class="input-group-text col-2 p-0 position-relative" for="loginPassword">
                            <i class="bi bi-key-fill fs-3 position-absolute top-50 start-50 translate-middle"></i>
                        </span>
                        <div class="form-floating col-10">
                            <input type="password" name="password" id="loginPassword" class="form-control rounded-0 rounded-end" autocomplete="off"
                                placeholder="**************" aria-label="Password" aria-describedby="" required
                                minlength="8" pattern="(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}">
                            <label for="loginPassword">Contraseña</label>

                        </div>
                    </div>
                    <div class="invalid-feedback">
                    </div>
                    <button class="border-0 btn btn-lg btn-outline-dark btn-primary btn-secondary my-3 shadow text-white w-100" type="submit" form="formLogIn">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </button>
                    <p class="mb-3">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#passwordRecoveryModal" class="link-body-emphasis text-black-50 text-decoration-none">
                            ¿Olvidó su contraseña?<i class="bi bi-person-fill-gear fs-3 px-1"></i>
                        </a>
                    </p>
                    <p class="mt-5 mb-3 text-muted user-select-none">&copy; 2025</p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="passwordRecoveryModal" tabindex="-1" aria-labelledby="passwordRecoveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark bg-gradient text-white shadow">
                <h5 class="modal-title" id="passwordRecoveryModalLabel">Olvidó su contraseña</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="formpasswordRecovery" class="" method="POST" novalidate>
                    <p>Ingrese la dirección de correo electrónico a la que desea que se envíe la información para restablecer la contraseña.</p>
                    <div class="input-group mb-3 row-cols-2">
                        <span class="input-group-text col-2 p-0 position-relative" id="basic-addon1">
                            <i
                                class="bi bi-envelope-fill fs-3 position-absolute top-50 start-50 translate-middle"></i>
                        </span>
                        <div class="form-floating col-10">
                            <input type="email" name="email" id="resetEmail"
                                class="form-control rounded-0 rounded-end" placeholder="Password reset email"
                                aria-label="Username" aria-describedby="basic-addon1" required>
                            <label for="resetEmail">Correo electrónico</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-target="#modalLogin" data-bs-toggle="modal">Volver al inicio de sesión</button>
                <button type="submit" class="border-0 btn btn-outline-dark btn-primary btn-secondary my-3 shadow text-white" form="formPasswordRecovery">Enviar solicitud <i class="bi bi-send-fill"></i></button>
            </div>
        </div>
    </div>
</div>