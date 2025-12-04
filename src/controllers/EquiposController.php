<?php
require_once __DIR__ . '/../models/EquipoModel.php';
require_once __DIR__ . '/../models/MovimientoEquipoModel.php';
require_once __DIR__ . '/../models/EmpleadoModel.php';
require_once __DIR__ . '/../models/AreaModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/sanitize.php';
require_once __DIR__ . '/../helpers/permisos.php';

class EquiposController
{
    private $equipoModel;
    private $movimientoModel;
    private $empleadoModel;
    private $areaModel;
    private $userModel;

    public function __construct()
    {
        $this->equipoModel = new EquipoModel();
        $this->movimientoModel = new MovimientoEquipoModel();
        $this->empleadoModel = new EmpleadoModel();
        $this->areaModel = new AreaModel();
        $this->userModel = new UserModel();
    }

    // ====================================================================
    // API REST METHODS (Nombres limpios)
    // ====================================================================

    /**
     * GET ?equipos
     * Obtiene listado con filtros y paginación
     */
    public function getAll()
    {
        checkAuth();

        if (!Permisos::tienePermiso('listar_equipos')) {
            $this->jsonResponse(['success' => false, 'message' => 'Acceso denegado.'], 403);
        }

        $search = sanitizeInput($_GET['search'] ?? '');
        $sort   = sanitizeInput($_GET['sort'] ?? 'id');
        $order  = sanitizeInput($_GET['order'] ?? 'ASC');
        $limit  = isset($_GET['limit']) ? sanitizeInt($_GET['limit']) : 10;
        $page   = isset($_GET['page']) ? sanitizeInt($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

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
    public function getOne()
    {
        checkAuth();

        if (!Permisos::tienePermiso('consultar_equipo')) {
            $this->jsonResponse(['success' => false, 'message' => 'Acceso denegado.'], 403);
        }

        $id = isset($_GET['id']) ? sanitizeInt($_GET['id']) : 0;
        $equipo = $this->equipoModel->getById($id);

        if ($equipo) {
            $this->jsonResponse(['success' => true, 'data' => $equipo]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        }
    }

    /**
     * POST ?equipos
     * Crea un nuevo recurso
     */
    public function create()
    {
        checkAuth();

        if (!Permisos::tienePermiso('crear_equipo')) {
            $this->jsonResponse(['success' => false, 'message' => 'No tienes permiso para registrar equipos.'], 403);
        }

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

        if (!empty($data['numero_serie'])) {
            if ($this->equipoModel->existeNumeroSerie($data['numero_serie'])) {
                $this->jsonResponse(['success' => false, 'message' => 'El número de serie ya existe'], 409);
            }
        }

        // --- LÓGICA DE ASIGNACIÓN EN ALTA ---
        // Capturar los datos del input
        $asignadoTipo = $dataRaw['asignado_tipo'] ?? null;
        $asignadoId   = isset($dataRaw['asignado_id']) ? sanitizeInt($dataRaw['asignado_id']) : null;
        $nombreAsignado = ""; // Para usar en el historial

        if ($data['estado'] === 'Asignado') {
            if (empty($asignadoTipo) || empty($asignadoId)) {
                $this->jsonResponse(['success' => false, 'message' => 'Debe seleccionar a quién asignar el equipo.'], 400);
            }

            // Generar el texto para "ubicacion_detalle" y almacenar para el historial
            $nombreUbicacion = "Asignado";

            if ($asignadoTipo === 'usuario') {
                $u = $this->userModel->findById($asignadoId);
                if ($u) $nombreUbicacion = "Usuario: " . $u['nombre'] . " " . $u['apellido'];
            } elseif ($asignadoTipo === 'empleado') {
                $e = $this->empleadoModel->getById($asignadoId);
                if ($e) $nombreUbicacion = "Empleado: " . $e['nombre'] . " " . $e['apellido'];
            } elseif ($asignadoTipo === 'area') {
                $a = $this->areaModel->getById($asignadoId);
                if ($a) $nombreUbicacion = "Área: " . $a['nombre'];
            }

            // Guardamos en el array data que irá al modelo
            $data['asignado_tipo'] = $asignadoTipo;
            $data['asignado_id'] = $asignadoId;
            $data['ubicacion_detalle'] = $nombreUbicacion; // Sobrescribimos lo que haya puesto el usuario manualmente

            $nombreAsignado = "Asignado: ($nombreUbicacion)"; // Guardamos para el texto del historial

        } else {
            // Si nace como disponible, limpiamos relaciones
            $data['asignado_tipo'] = null;
            $data['asignado_id'] = null;
            // Si no escribió ubicación manual, ponemos default
            if ($data['estado'] === 'Disponible' && empty($data['ubicacion_detalle'])) {
                $data['ubicacion_detalle'] = 'Depósito IT';
            }
        }

        // --- Determinar Tipo de Movimiento Dinámico ---
        $tipoMovimiento = 'Alta'; // Valor por defecto (para 'Disponible')
        $obsInicio = "Alta inicial en stock."; // Texto base

        switch ($data['estado']) {
            case 'Asignado':
                $tipoMovimiento = 'Asignacion';
                $obsInicio = "Ingreso directo con asignación. " . $nombreAsignado . ".";
                break;

            case 'En reparacion':
                $tipoMovimiento = 'Reparacion';
                $obsInicio = "Ingreso directo a servicio técnico/reparación.";
                break;

            case 'Baja':
                $tipoMovimiento = 'Baja';
                $obsInicio = "Registro histórico de equipo dado de baja.";
                break;

            case 'Disponible':
            default:
                $tipoMovimiento = 'Alta';
                $obsInicio = "Alta inicial. Equipo disponible en stock.";
                break;
        }

        // Concatenamos la observación del sistema con la nota del usuario
        $obs = $obsInicio . " " . ($data['observaciones'] ?? '');

        try {
            $nuevoId = $this->equipoModel->create($data);

            if ($nuevoId > 0) {
                $currentUser = currentUser();   // Obtener el usuario actual para saber quién hizo el alta.
                $idUsuario = $currentUser['id'];

                $this->movimientoModel->registrar($nuevoId, $idUsuario, $tipoMovimiento, $obs);

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
    public function update()
    {
        checkAuth();

        if (!Permisos::tienePermiso('editar_equipo')) {
            $this->jsonResponse(['success' => false, 'message' => 'No tienes permiso para editar equipos.'], 403);
        }

        // En PUT los datos siempre vienen en php://input
        $input = json_decode(file_get_contents("php://input"), true);
        $id = isset($input['id']) ? sanitizeInt($input['id']) : 0;

        if ($id <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'ID inválido para actualización'], 400);
        }

        // Obtenemos estado previo
        $equipoActual = $this->equipoModel->getById($id);
        if (!$equipoActual) {
            $this->jsonResponse(['success' => false, 'message' => 'Equipo no encontrado'], 404);
        }
        // Si está de Baja, no se puede editar.
        if ($equipoActual['estado'] === 'Baja') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'No se puede editar un equipo que ya ha sido dado de Baja definitiva.'
            ], 400);
        }

        // Sanitizar y Validar
        $data = $this->sanitizeData($input);
        $motivoUsuario = sanitizeInput($input['motivo_cambio'] ?? '');

        // Validar si es edición
        if (empty($motivoUsuario)) {
            $this->jsonResponse(['success' => false, 'message' => 'Debe indicar un motivo para realizar la edición.'], 400);
        }

        // Validar código duplicado (excluyendo el actual)
        if ($this->equipoModel->existeCodigo($data['codigo_inventario'], $id)) {
            $this->jsonResponse(['success' => false, 'message' => 'El código ya existe en otro equipo'], 409);
        }
        // Validar serie duplicada (excluyendo el actual)
        if (!empty($data['numero_serie'])) {
            if ($this->equipoModel->existeNumeroSerie($data['numero_serie'], $id)) {
                $this->jsonResponse(['success' => false, 'message' => 'El número de serie ya existe en otro equipo'], 409);
            }
        }

        // --- LÓGICA DE ASIGNACIÓN ---
        $asignadoTipo = $input['asignado_tipo'] ?? null;
        $asignadoId   = isset($input['asignado_id']) ? sanitizeInt($input['asignado_id']) : null;

        if ($data['estado'] === 'Asignado') {
            if (empty($asignadoTipo) || empty($asignadoId)) {
                $this->jsonResponse(['success' => false, 'message' => 'Debe seleccionar a quién asignar el equipo.'], 400);
            }

            // Generamos el texto para "ubicacion_detalle"
            $nombreAsignado = "Asignado";
            if ($asignadoTipo === 'usuario') {
                $u = $this->userModel->findById($asignadoId);
                if ($u) $nombreAsignado = "Usuario: " . $u['nombre'] . " " . $u['apellido'];
            } elseif ($asignadoTipo === 'empleado') {
                $e = $this->empleadoModel->getById($asignadoId);
                if ($e) $nombreAsignado = "Empleado: " . $e['nombre'] . " " . $e['apellido'];
            } elseif ($asignadoTipo === 'area') {
                $a = $this->areaModel->getById($asignadoId);
                if ($a) $nombreAsignado = "Área: " . $a['nombre'];
            }

            $data['asignado_tipo'] = $asignadoTipo;
            $data['asignado_id'] = $asignadoId;
            $data['ubicacion_detalle'] = $nombreAsignado; // Sobrescribimos la ubicación manual

        } else {
            // Si NO está asignado, limpiamos
            $data['asignado_tipo'] = null;
            $data['asignado_id'] = null;

            // Si pasa a disponible y no escribieron ubicación, ponemos una por defecto
            if ($data['estado'] === 'Disponible' && empty($data['ubicacion_detalle'])) {
                $data['ubicacion_detalle'] = 'Depósito IT';
            }
        }

        try {
            if ($this->equipoModel->update($id, $data)) {

                // --- GENERACIÓN DE TRAZABILIDAD ---
                $cambiosDetectados = [];

                // Campos que queremos auditar
                $camposAuditoría = [
                    'codigo_inventario' => 'Código',
                    'marca' => 'Marca',
                    'modelo' => 'Modelo',
                    'numero_serie' => 'Serie',
                    'estado' => 'Estado',
                    'ubicacion_detalle' => 'Ubicación'
                ];

                foreach ($camposAuditoría as $campoDB => $label) {
                    $valorViejo = trim($equipoActual[$campoDB] ?? '');
                    $valorNuevo = trim($data[$campoDB] ?? '');

                    if ($valorViejo !== $valorNuevo) {
                        $cambiosDetectados[] = "$label: '$valorViejo' -> '$valorNuevo'";
                    }
                }

                // --- DETECCIÓN DE CAMBIOS PARA HISTORIAL ---
                // Determinamos Tipo de Movimiento
                $tipoMov = 'Ajuste'; // Por defecto es una simple edición de datos

                // Si cambió el estado, tiene prioridad el tipo de movimiento de estado
                $nuevoEstado = $data['estado'];
                $estadoAnterior = $equipoActual['estado'];

                if ($nuevoEstado !== $estadoAnterior) {
                    if ($estadoAnterior === 'Disponible' && $nuevoEstado === 'Asignado') $tipoMov = 'Asignacion';
                    elseif ($estadoAnterior === 'Asignado' && $nuevoEstado === 'Disponible') $tipoMov = 'Devolucion';
                    elseif ($nuevoEstado === 'En reparacion') $tipoMov = 'Reparacion';
                    elseif ($nuevoEstado === 'Baja') $tipoMov = 'Baja';
                }

                // Construimos la observación final para el historial
                // Formato: [MOTIVO DEL USUARIO] - [DETALLE DE CAMBIOS]
                $obsHistorial = "Motivo: " . $motivoUsuario;

                if (!empty($cambiosDetectados)) {
                    $obsHistorial .= " - " . implode(', ', $cambiosDetectados) . ".";
                }

                // Registramos siempre que haya cambios O un motivo explícito
                $idUsuario = currentUser()['id'];
                $this->movimientoModel->registrar($id, $idUsuario, $tipoMov, $obsHistorial);


                $this->jsonResponse(['success' => true, 'message' => 'Equipo actualizado correctamente']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'No se pudo actualizar'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    /**
     * GET ?historial&id=X
     * Consultar historial de un equipo
     */
    public function getHistorial()
    {
        checkAuth();
        if (!Permisos::tienePermiso('consultar_historial_equipo')) {
            $this->jsonResponse(['success' => false, 'message' => 'Acceso denegado. No tienes permisos para ver el historial de los equipos.'], 403);
        }

        $id = isset($_GET['id']) ? sanitizeInt($_GET['id']) : 0;
        if ($id <= 0) $this->jsonResponse(['success' => false, 'message' => 'ID inválido'], 400);

        try {
            $data = $this->movimientoModel->getByEquipo($id);
            $this->jsonResponse(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error al obtener historial'], 500);
        }
    }

    /**
     * DELETE ?equipos
     */
    public function delete()
    {
        checkAuth();

        if (!Permisos::tienePermiso('eliminar_equipo')) {
            $this->jsonResponse(['success' => false, 'message' => 'No tienes permiso para eliminar equipos.'], 403);
        }

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

    /**
     * Maneja las solicitudes GET para el recurso "equipos".
     */
    public function handleGet(array $params)
    {
        if (isset($params['historial'])) {
            $this->getHistorial();
        } elseif (isset($params['id'])) {
            $this->getOne();
        } else {
            $this->getAll();
        }
    }


    // ====================================================================
    // HELPERS PRIVADOS
    // ====================================================================

    /**
     * Función para enviar respuestas JSON consistentes.
     * */
    private function jsonResponse($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    /**
     * Centraliza la limpieza de datos para Create y Update
     */
    private function sanitizeData($raw)
    {
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
