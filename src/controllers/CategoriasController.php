<?php
require_once __DIR__ . '/../helpers/permisos.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../models/CategoriaModel.php';

class CategoriasController
{
    private $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    /**
     * GET ?categorias
     */
    public function getAll()
    {

        checkAuth();

        switch (true) {
            case Permisos::tienePermiso('listar_categorias'):
                // Puede ver todas las categorías
                break;
            case Permisos::tienePermiso('listar_categorias_activas'):
                // Puede ver solo categorías activas
                try {
                    $categorias = $this->categoriaModel->getAllActive();
                    $this->jsonResponse(['success' => true, 'data' => $categorias]);
                } catch (Exception $e) {
                    $this->jsonResponse(['success' => false, 'message' => 'Error al cargar categorías'], 500);
                }

            default:
                $this->jsonResponse(['success' => false, 'message' => 'Acceso denegado.'], 403);
        }
    }

    /**
     * Función para enviar respuestas JSON consistentes.
     * */
    private function jsonResponse($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
