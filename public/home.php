<?php
$extra_css = [];
$extra_js  = ['/assets/js/login.js'];
?>

<?php require_once __DIR__ . '/../src/views/layouts/header.php'; ?>

<?php require_once __DIR__ . '/../src/views/layouts/navbar.php'; ?>

<div class="container">
    <div class="h1 text-center">
        Acerca de nuestros servicios
    </div>
</div>
<div class="row row-cols-3 g-0">
    <div class="col">
        <img src="<?php echo BASE_URL ?>/assets/img/AnalyticsGraph.gif" alt="" class="img-fluid vw-100">
    </div>
    <div class="col">
        <img src="<?php echo BASE_URL ?>/assets/img/AnalyticsLaptop.gif" alt="" class="img-fluid w-100">
    </div>
    <div class="col">
        <img src="<?php echo BASE_URL ?>/assets/img/AnalyticsPeople.gif" alt="" class="img-fluid w-100">
    </div>
</div>

<?php require_once __DIR__ . '/../src/views/auth/modal-login.php'; ?>

<?php require_once __DIR__ . '/../src/views/layouts/footer.php'; ?>