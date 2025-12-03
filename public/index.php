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
// Control de acceso Global
// ========================================
$publicRoutes = ['home']; // rutas accesibles sin sesión
$user = currentUser();

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
// A. RUTAS MVC (Controladores + Permisos)
// ========================================
// Estas rutas instancian un controlador que verifica permisos antes de cargar la vista.

switch ($route) {
    case 'usuarios':    // Módulo Usuarios
        require_once __DIR__ . '/../src/controllers/UsersController.php';
        $controller = new UsersController();
        $controller->index(); // Valida 'ver_usuarios'
        exit;
    case 'roles':       // Módulo Roles
        require_once __DIR__ . '/../src/controllers/RolesController.php';
        $controller = new RolesController();
        $controller->index(); // Valida 'ver_roles'
        exit;

    case 'perfil':      // Perfil Usuario
        require_once __DIR__ . '/../src/controllers/ProfileController.php';
        $controller = new ProfileController();
        $controller->index();
        exit;
    case 'inventario':
        require_once __DIR__ . '/../src/controllers/InventarioController.php';
        $controller = new InventarioController();
        $controller->index();
        exit;
}

// ========================================
// B. RUTAS LEGACY (Vistas directas)
// ========================================
// Estas rutas cargan el archivo PHP directamente.
// Nota: 'usuarios', 'roles' e 'inventario' se eliminaron de aquí.

$legacyRoutes = [
    // Página pública
    'home'                  => __DIR__ . '/home.php',

    // Páginas generales
    'inicio'                => __DIR__ . '/../src/views/dashboard/index.php',

    // Cerrar sesión
    'logout'                => __DIR__ . '/../src/views/auth/logout.php',
];

// Cargar ruta legacy si existe
if (isset($legacyRoutes[$route])) {
    require_once $legacyRoutes[$route];
    exit;
}

// ========================================
// Error 404 - Página no encontrada
// ========================================
http_response_code(404);
require_once __DIR__ . '/../src/views/errors/404.php';
