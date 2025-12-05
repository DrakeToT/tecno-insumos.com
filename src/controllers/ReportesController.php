<?php
require_once __DIR__ . '/../models/ReporteModel.php';
require_once __DIR__ . '/../models/MovimientoEquipoModel.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/sanitize.php';
require_once __DIR__ . '/../helpers/permisos.php';

class ReportesController
{
    private $reporteModel;
    private $movimientoModel;

    public function __construct()
    {
        $this->reporteModel = new ReporteModel();
        $this->movimientoModel = new MovimientoEquipoModel();
    }

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
        if (!Permisos::tienePermiso('ver_reportes')) {
            http_response_code(403);
            require_once __DIR__ . '/../views/errors/403.php'; // Carga la vista de error
            exit; // Detiene la carga del resto de la página
        }

        require_once __DIR__ . '/../views/reportes/index.php';
    }

    /**
     * GET ?reportes&accion=asignaciones&tipo=usuario&id=1
     * Devuelve: Datos de la entidad + Lista de Equipos + Historial de cada equipo
     */
    public function getReporteAsignaciones()
    {
        checkAuth();

        // Validar permiso general de reportes
        if (!Permisos::tienePermiso('ver_reportes')) {
            $this->jsonResponse(['success' => false, 'message' => 'No tiene permiso para ver reportes.'], 403);
        }

        $tipo = sanitizeInput($_GET['tipo'] ?? '');
        $id   = isset($_GET['id']) ? sanitizeInt($_GET['id']) : 0;

        // Validaciones básicas
        if (!in_array($tipo, ['usuario', 'empleado', 'area'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Tipo de entidad inválido. Use usuario, empleado o area.'], 400);
        }
        if ($id <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'ID inválido.'], 400);
        }

        try {
            // Obtener datos del Responsable (Usuario/Empleado/Área)
            $entidad = $this->reporteModel->getDatosEntidad($tipo, $id);

            if (!$entidad) {
                $this->jsonResponse(['success' => false, 'message' => 'La entidad solicitada no existe.'], 404);
            }

            // Obtener los equipos que tiene asignados actualmente
            $equipos = $this->reporteModel->getEquiposAsignados($tipo, $id);

            // Para cada equipo se agrega su historial de movimientos
            foreach ($equipos as &$equipo) {
                $equipo['historial'] = $this->movimientoModel->getByEquipo($equipo['id']);
            }

            // Estructura de respuesta final
            $reporte = [
                'responsable' => $entidad,
                'cantidad_equipos' => count($equipos),
                'equipos' => $equipos
            ];

            $this->jsonResponse(['success' => true, 'data' => $reporte]);

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error al generar el reporte: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper para respuesta JSON
     */
    private function jsonResponse($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}