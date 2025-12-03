<?php
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/permisos.php';

class InventarioController
{
    /**
     * Renderiza la vista principal
     */
    public function index()
    {
        if (!isUserLoggedIn()) {
            header('Location: ' . BASE_URL);
            exit;
        }
        // Valida Permiso
        if (!Permisos::tienePermiso('acceder_inventario')) {
            http_response_code(403);
            require_once __DIR__ . '/../views/errors/403.php'; // Carga la vista de error
            exit; // Detiene la carga del resto de la página
        }

        require_once __DIR__ . '/../views/inventario/index.php';
    }
}