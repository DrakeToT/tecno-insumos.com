<?php
require_once __DIR__ . '/../../../src/helpers/session.php';
require_once __DIR__ . '/../../../src/config/base-url.php';

$rol = $_SESSION['user']['rol']['nombre'];
$titlePage = "Perfil del {$rol} - Tecno Insumos";
$extra_css = ["/assets/css/perfil.css"];
$extra_js  = ['/assets/js/perfil.js'];
$page = 'perfil';
?>

<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>



<?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white text-center">
            <h5 class="mb-0">Perfil del <?php echo $rol ?></h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <h5 class="fw-bold mb-3">Foto de perfil</h5>
                    <div class="perfil-foto-container shadow">
                        <img class="avatar avatar-lg" id="fotoPerfil" src="<?php echo $urlFoto; ?>" alt="Foto de perfil">
                        <div class="perfil-overlay-editar">
                            <i class="bi bi-person-bounding-box"></i>
                        </div>
                    </div>
                    <form id="formFoto" enctype="multipart/form-data" class="d-none">
                        <input type="file" name="fotoPerfil" id="inputFoto" accept="image/*">
                    </form>
                </div>

                <div class="col-md-8">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="fw-bold">Datos personales</h5>
                        <button id="btnEditar" type="button" class="btn btn-outline-dark btn-sm">
                            <i class="bi bi-pencil-fill"></i> Editar
                        </button>
                    </div>

                    <form id="formDatos" class="needs-validation" novalidate>
                        <div class="mb-3 row row-cols-1 row-cols-md-2 g-2">
                            <div class="col">
                                <label class="form-label" for="nombrePerfil">Nombre</label>
                                <input type="text" id="nombrePerfil" class="form-control border-dark-subtle focus-ring focus-ring-dark" readonly required>
                            </div>
                            <div class="col">
                                <label class="form-label" for="apellidoPerfil">Apellido</label>
                                <input type="text" id="apellidoPerfil" class="form-control border-dark-subtle focus-ring focus-ring-dark" readonly required>
                            </div>
                            <div class="col w-100">
                                <label class="form-label" for="emailPerfil">Email</label>
                                <input type="email" id="emailPerfil" class="form-control border-dark-subtle focus-ring focus-ring-dark" readonly required>
                            </div>
                            <div class="col w-100">
                                <label class="form-label" for="passwordPerfil">Contraseña</label>
                                <div class="row g-2 align-items-center">
                                    <div class="col-12 col-md-9">
                                        <input type="password" id="passwordPerfil" class="form-control border-dark-subtle focus-ring focus-ring-dark" value="**************" readonly>
                                    </div>
                                    <div class="col-12 col-md-3 text-md-end">
                                        <button type="button"
                                            class="btn btn-outline-dark w-100 w-md-auto text-nowrap"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalPassword">
                                            <i class="bi bi-key-fill"></i> Cambiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button id="btnGuardar" type="submit" class="btn btn-success btn-sm d-none">
                                <i class="bi bi-save"></i> Guardar
                            </button>
                            <button id="btnCancelar" type="button" class="btn btn-secondary btn-sm d-none">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/../layouts/modals/modal-message.php';
require_once __DIR__ . '/../layouts/modals/modal-password.php';

require_once __DIR__ . '/../layouts/footer.php';
?>

