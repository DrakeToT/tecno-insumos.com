<?php
require_once __DIR__ . '/../models/EquipoModel.php';
require_once __DIR__ . '/../models/CategoriaModel.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/sanitize.php';

class EquiposController {
    private $equipoModel;
    private $categoriaModel;

    public function __construct() {
        $this->equipoModel = new EquipoModel();
        $this->categoriaModel = new CategoriaModel();
    }

    // Renderiza la vista (HTML)
    public function index() {
        if (!isUserLoggedIn()) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        require_once __DIR__ . '/../views/modules/inventario/index.php';
    }

    // ====================================================================
    // API REST METHODS (Nombres limpios)
    // ====================================================================

    /**
     * GET ?equipos
     * Obtiene listado con filtros y paginación
     */
    public function getAll() {
        $this->checkAuth();

        $search = sanitizeInput($_GET['search'] ?? '');
        $sort   = sanitizeInput($_GET['sort'] ?? 'id');
        $order  = sanitizeInput($_GET['order'] ?? 'ASC');
        $limit  = isset($_GET['limit']) ? sanitizeInt($_GET['limit']) : 10;
        $offset = isset($_GET['offset']) ? sanitizeInt($_GET['offset']) : 0;

        try {
            $data = $this->equipoModel->getAll($search, $sort, $order, $limit, $offset);
            $total = $this->equipoModel->countAll($search);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $total,
                    'page' => ($limit > 0) ? floor($offset / $limit) + 1 : 1,
                    'limit' => $limit,
                    'pages' => ($limit > 0) ? ceil($total / $limit) : 0
                ]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET ?equipos&id=X
     * Obtiene un solo recurso
     */
    public function getOne() {
        $this->checkAuth();
        $id = isset($_GET['id']) ? sanitizeInt($_GET['id']) : 0;
        $equipo = $this->equipoModel->getById($id);
        
        if ($equipo) {
            $this->jsonResponse(['success' => true, 'data' => $equipo]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        }
    }

    /**
     * GET ?categorias
     */
    public function getCategorias() {
        $this->checkAuth();
        try {
            $categorias = $this->categoriaModel->getAllActive();
            $this->jsonResponse(['success' => true, 'data' => $categorias]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error al cargar categorías'], 500);
        }
    }

    /**
     * POST ?equipos
     * Crea un nuevo recurso
     */
    public function create() {
        $this->checkAuth();

        // Leer JSON Body
        $input = json_decode(file_get_contents("php://input"), true);
        $dataRaw = (json_last_error() === JSON_ERROR_NONE && is_array($input)) ? $input : $_POST;

        // Sanitizar y Validar
        $data = $this->sanitizeData($dataRaw);
        
        if (empty($data['codigo_inventario']) || empty($data['marca']) || $data['id_categoria'] <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Complete los campos obligatorios'], 400);
        }

        if ($this->equipoModel->existeCodigo($data['codigo_inventario'])) {
            $this->jsonResponse(['success' => false, 'message' => 'El código ya existe'], 409);
        }

        try {
            if ($this->equipoModel->create($data)) {
                $this->jsonResponse(['success' => true, 'message' => 'Equipo registrado correctamente'], 201);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Error al guardar en BD'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * PUT ?equipos
     * Actualiza un recurso existente
     */
    public function update() {
        $this->checkAuth();

        // En PUT los datos siempre vienen en php://input
        $input = json_decode(file_get_contents("php://input"), true);
        $id = isset($input['id']) ? sanitizeInt($input['id']) : 0;

        if ($id <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'ID inválido para actualización'], 400);
        }

        $data = $this->sanitizeData($input);

        // Validar código duplicado (excluyendo el actual)
        if ($this->equipoModel->existeCodigo($data['codigo_inventario'], $id)) {
            $this->jsonResponse(['success' => false, 'message' => 'El código ya existe en otro equipo'], 409);
        }

        try {
            if ($this->equipoModel->update($id, $data)) {
                $this->jsonResponse(['success' => true, 'message' => 'Equipo actualizado correctamente']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'No se pudo actualizar'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * DELETE ?equipos
     */
    public function delete() {
        $this->checkAuth();

        $input = json_decode(file_get_contents("php://input"), true);
        $id = isset($input['id']) ? sanitizeInt($input['id']) : 0;

        if ($id <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'ID inválido'], 400);
        }

        if ($this->equipoModel->delete($id)) {
            $this->jsonResponse(['success' => true, 'message' => 'Equipo eliminado']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'No se pudo eliminar'], 500);
        }
    }

    // ====================================================================
    // HELPERS PRIVADOS
    // ====================================================================

    private function checkAuth() {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        if (!isUserLoggedIn()) {
            echo json_encode(["success" => false, "message" => "Acceso no autorizado."]);
            http_response_code(403);
            exit;
        }
    }

    private function jsonResponse($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    /**
     * Centraliza la limpieza de datos para Create y Update
     */
    private function sanitizeData($raw) {
        return [
            'codigo_inventario' => sanitizeInput($raw['codigo_inventario'] ?? ''),
            'id_categoria'      => sanitizeInt($raw['id_categoria'] ?? 0),
            'marca'             => sanitizeInput($raw['marca'] ?? ''),
            'modelo'            => sanitizeInput($raw['modelo'] ?? ''),
            'numero_serie'      => sanitizeInput($raw['numero_serie'] ?? ''),
            'estado'            => sanitizeInput($raw['estado'] ?? 'Disponible'),
            'ubicacion_detalle' => sanitizeInput($raw['ubicacion_detalle'] ?? ''),
            'fecha_adquisicion' => sanitizeInput($raw['fecha_adquisicion'] ?? ''),
            'proveedor'         => sanitizeInput($raw['proveedor'] ?? ''),
            'valor_compra'      => isset($raw['valor_compra']) ? sanitizeFloat($raw['valor_compra']) : null,
            'observaciones'     => sanitizeInput($raw['observaciones'] ?? '')
        ];
    }
}
?>