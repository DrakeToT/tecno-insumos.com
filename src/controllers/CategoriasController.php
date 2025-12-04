<?php
require_once __DIR__ . '/../helpers/permisos.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/sanitize.php';
require_once __DIR__ . '/../models/CategoriaModel.php';

class CategoriasController
{
    private $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    /**
     * GET ?categoria&id=#
     */
    public function getById()
    {

        checkAuth();

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'ID de categoría inválido.'], 400);
        }

        $id = sanitizeInt(trim($_GET['id']));

        try {
            switch ($id > 0) {
                case Permisos::tienePermiso('consultar_categoria'):
                    // Puede ver cualquier categoría
                    break;
                case Permisos::tienePermiso('consultar_categoria_activa'):
                    // Puede ver solo categorías activas
                    $id = intval($_GET['id']);
                    $categoria = $this->categoriaModel->getActiveById($id);
                    if ($categoria && $categoria['estado' !== 'Activo']) {
                        $this->jsonResponse(['success' => false, 'message' => 'Acceso denegado.'], 403);
                    }
                    $this->jsonResponse(['success' => true, 'data' => $categoria]);
                    break;
                default:
                    $this->jsonResponse(['success' => false, 'message' => 'Acceso denegado.'], 403);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error al cargar la categoría'], 500);
        }
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
