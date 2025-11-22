<?php
// ========================================
// Router principal del sistema Tecno Insumos
// ========================================

require_once __DIR__ . '/../src/config/base-url.php';
require_once __DIR__ . '/../src/helpers/session.php';
require_once __DIR__ . '/../src/helpers/sanitize.php';

// Obtener la ruta solicitada (por defecto: home)
$route = sanitizeInput($_GET['route'] ?? 'home');

// ========================================
// Definición de rutas disponibles
// ========================================
$routes = [
    // Página pública
    'home'                  => __DIR__ . '/home.php',
    
    // Páginas para usuarios autenticados
    'inicio'                => __DIR__ . '/../src/views/dashboard/index.php',
    'usuarios'              => __DIR__ . '/../src/views/usuarios/index.php',
    'roles'                 => __DIR__ . '/../src/views/roles/index.php',
    'configuracion'         => __DIR__ . '/../src/views/configuracion/index.php',
    'perfil'                => __DIR__ . '/../src/views/perfil/index.php',
    'inventario'            => __DIR__ . '/../src/views/inventario/index.php',
    
    // Cerrar sesión
    'logout'                => __DIR__ . '/../src/views/auth/logout.php',
    
    // Panel del administrador
    'admin/home'            => __DIR__ . '/../src/views/admin/home.php',
    'admin/users'           => __DIR__ . '/../src/views/admin/users.php',
    'admin/roles'           => __DIR__ . '/../src/views/admin/roles.php',
    'admin/configuration'   => __DIR__ . '/../src/views/admin/configuration.php',

    // Otros roles
    'stock/home'            => __DIR__ . '/../src/views/stock/home.php',
    'soporte/home'          => __DIR__ . '/../src/views/soporte/home.php',
    'coordinador/home'      => __DIR__ . '/../src/views/coordinador/home.php',
];

// ========================================
// Control de acceso
// ========================================
$publicRoutes = ['home']; // rutas accesibles sin sesión
$user = currentUser();
$idRol = $user['rol']['id'] ?? null;

// Si intenta acceder a una ruta privada sin sesión → redirigir al home
if (!in_array($route, $publicRoutes) && !$user) {
    header('Location: ' . BASE_URL . '/home');
    exit;
}

// Si el usuario logueado intenta ir a 'home' redireciona al módulo principal
if ($user && $route === 'home') {
    header('Location: ' . BASE_URL . '/inicio');
    exit;
}


// ========================================
// Cargar la vista correspondiente
// ========================================
if (isset($routes[$route])) {
    require_once $routes[$route];
    exit;
}

// ========================================
// Error 404 - Página no encontrada
// ========================================
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-center d-flex align-items-center justify-content-center vh-100">
    <div>
        <h1 class="display-3 text-danger">404</h1>
        <p class="lead">La página que buscas no existe o ha sido movida.</p>
        <a href="<?php echo BASE_URL; ?>/home" class="btn btn-primary">Volver al inicio</a>
    </div>
</body>
</html>
