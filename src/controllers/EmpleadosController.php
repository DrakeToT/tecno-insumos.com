<?php
require_once __DIR__ . '/../helpers/permisos.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';

class EmpleadosController{
    private $empleadoModel;

    public function __construct()
    {
        $this->empleadoModel = new EmpleadoModel();
    }

    /**
     * GET ?empleados
     */
    public function getAll()
    {
        checkAuth();

        switch (true) {
            case Permisos::tienePermiso('listar_empleados'):
                // Puede ver todos los empleados
                break;
            case Permisos::tienePermiso('listar_empleados_activos'):
                // Puede ver solo empleados activos
                try {
                    $data = $this->empleadoModel->getAllActive();
                    $this->jsonResponse(['success' => true, 'data' => $data]);
                } catch (Exception $e) {
                    $this->jsonResponse(['success' => false, 'message' => 'Error al cargar empleados'], 500);
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