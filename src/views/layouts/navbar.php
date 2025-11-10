<?php
require_once __DIR__ . '/../../helpers/session.php';
require_once __DIR__ . '/../../helpers/profile-image.php';
require_once __DIR__ . '/../../config/menu.php';

$user = currentUser();
$menus = require __DIR__ . '/../../config/menu.php';
?>

<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark bg-gradient py-0" aria-label="Tecno Insumos">
    <div class="container-fluid g-1 g-lg-5">
        <div class="navbar-brand fs-1 text-center user-select-none pt-0">
            <i class="bi bi-upc-scan"></i>
            <span class="d-block d-sm-inline">Tecno Insumos</span>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar" aria-label="Alternar navegación">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbar"
            aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header d-flex d-lg-none justify-content-between align-items-center">
                <?php if ($user): ?>
                    <div class="d-flex gap-3 justify-content-center">
                        <div class="nav-item dropdown">
                            <a class="nav-link icon-link-hover p-0 d-flex flex-row flex-lg-column align-items-center justify-content-center lh-1 <?php echo ($page === 'perfil') ? 'active' : ''; ?>"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-lg-block text-center fs-6">
                                    <img class="avatar avatar-md" id="fotoNavHeader" src="<?php echo htmlspecialchars($urlFoto); ?>" alt="Foto de perfil">
                                </span>
                                <span class="d-flex align-items-center ms-2">
                                    <span>
                                        <?php
                                        echo htmlspecialchars(trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? ''))) ?: 'Usuario';
                                        ?>
                                    </span>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark shadow dropdown-menu-lg-end mt-2">
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>/perfil">
                                        <i class="bi bi-person-circle me-2"></i> Mi Perfil
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/logout">
                                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <button type="button" class="btn-close btn-close-white ms-auto"
                    data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>


            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end align-items-start align-items-lg-end flex-grow-1 pb-1 pe-3 gap-4">

                    <?php if (!$user): ?>
                        <!-- Menú público -->
                        <li class="nav-item">
                            <a class="nav-link icon-link-hover p-0 d-flex flex-row flex-lg-column align-items-center justify-content-center lh-1"
                                href="#" data-bs-toggle="modal" data-bs-target="#modalLogin">
                                <i class="bi bi-person-fill d-md-block text-center fs-3"></i>
                                <span class="ms-1 ms-lg-0">Iniciar Sesión</span>
                            </a>
                        </li>

                    <?php else: ?>
                        <!-- Menú interno dinámico -->
                        <?php
                        $rolActual = $user['rol']['nombre'] ?? '';
                        if (isset($menus[$rolActual])) {
                            foreach ($menus[$rolActual] as $item): ?>
                                <li class="nav-item">
                                    <a class="nav-link icon-link-hover p-0 d-flex flex-row flex-lg-column align-items-center justify-content-center lh-1 <?php echo $page === $item['page'] ? 'active' : ''; ?>"
                                        href="<?php echo BASE_URL . $item['route']; ?>">
                                        <i class="bi <?php echo $item['icon']; ?> fs-3 text-center"></i>
                                        <span class="ms-2 ms-lg-0"><?php echo htmlspecialchars($item['label']); ?></span>
                                    </a>
                                </li>
                        <?php endforeach;
                        }
                        ?>

                        <!-- Menú Perfil (común a todos) -->
                        <li class="nav-item dropdown d-none d-lg-block">
                            <a class="nav-link icon-link-hover p-0 d-flex flex-row flex-lg-column align-items-center justify-content-center lh-1 <?php echo $page === 'perfil' ? 'active' : ''; ?>"
                                href="#" id="dropdownPerfil" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <img class="avatar avatar-sm" src="<?php echo $urlFoto; ?>" alt="Foto" id="fotoNavBody">
                                <span><?php echo htmlspecialchars($user['nombre'] ?? 'Perfil'); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-lg-end shadow mt-sm-1 mt-2">
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>/perfil">
                                        <i class="bi bi-person-circle me-2"></i> Mi Perfil
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/logout">
                                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</nav>