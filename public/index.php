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
// Control de Acceso Global
// ========================================
$publicRoutes = ['home']; // rutas accesibles sin sesión
$user = currentUser();

// Si intenta acceder a una ruta privada sin sesión -> login
if (!in_array($route, $publicRoutes) && !$user) {
    header('Location: ' . BASE_URL . '/home');
    exit;
}

// Si usuario logueado va a home -> dashboard
if ($user && $route === 'home') {
    header('Location: ' . BASE_URL . '/inicio');
    exit;
}

// ========================================
// A. RUTAS MVC (Pasan por Controlador) - LA FORMA SEGURA
// ========================================
switch ($route) {
    case 'inventario':
        require_once __DIR__ . '/../src/controllers/EquiposController.php';
        $controller = new EquiposController();
        $controller->index(); // Aquí está la validación de seguridad
        exit; // Terminamos aquí, el controlador se encarga de la vista
        
    // Aquí irás agregando futuros módulos MVC (Insumos, Reportes, etc.)
}

// ========================================
// B. RUTAS LEGACY (Vistas directas)
// ========================================
// Estas rutas cargan la vista directamente. 
// Idealmente, deberías migrarlas a controladores poco a poco.
$legacyRoutes = [
    // Página pública
    'home'                  => __DIR__ . '/home.php',
    
    // Páginas internas
    'inicio'                => __DIR__ . '/../src/views/dashboard/index.php',
    'usuarios'              => __DIR__ . '/../src/views/usuarios/index.php',
    'roles'                 => __DIR__ . '/../src/views/roles/index.php',
    'configuracion'         => __DIR__ . '/../src/views/configuracion/index.php',
    'perfil'                => __DIR__ . '/../src/views/perfil/index.php',
    
    // Auth
    'logout'                => __DIR__ . '/../src/views/auth/logout.php',
    
    // Paneles específicos
    'admin/home'            => __DIR__ . '/../src/views/admin/home.php',
    'admin/users'           => __DIR__ . '/../src/views/admin/users.php',
    'admin/roles'           => __DIR__ . '/../src/views/admin/roles.php',
    'admin/configuration'   => __DIR__ . '/../src/views/admin/configuration.php',
    'stock/home'            => __DIR__ . '/../src/views/stock/home.php',
    'soporte/home'          => __DIR__ . '/../src/views/soporte/home.php',
    'coordinador/home'      => __DIR__ . '/../src/views/coordinador/home.php',
];

if (isset($legacyRoutes[$route])) {
    require_once $legacyRoutes[$route];
    exit;
}

// ========================================
// Error 404
// ========================================
http_response_code(404);
require_once __DIR__ . '/../src/views/errors/404.php'; // Usamos tu nueva vista 404
?>