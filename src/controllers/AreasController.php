<?php
require_once __DIR__ . '/../helpers/permisos.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../models/AreaModel.php';

class AreasController{
    private $areaModel;
    
    public function __construct()
    {
        $this->areaModel = new AreaModel();
    }

    /**
     * GET ?areas
     */
    public function getAll(){
        checkAuth();

        switch (true) {
            case Permisos::tienePermiso('listar_areas'):
                // Puede ver todas las áreas
                break;
            case Permisos::tienePermiso('listar_areas_activas'):
                // Puede ver solo áreas activas
                try {
                    $areas = $this->areaModel->getAllActive();
                    $this->jsonResponse(['success' => true, 'data' => $areas]);
                } catch (Exception $e) {
                    $this->jsonResponse(['success' => false, 'message' => 'Error al cargar áreas'], 500);
                }
                break;

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