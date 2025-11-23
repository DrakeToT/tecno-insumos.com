<?php
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/PermisoModel.php';
require_once __DIR__ . '/../helpers/sanitize.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/permisos.php';

class RolesController
{
    private $roleModel;
    private $permisoModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->permisoModel = new PermisoModel();
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

        // Validación visual 403
        if (!Permisos::tienePermiso('ver_roles')) {
            http_response_code(403);
            require_once __DIR__ . '/../views/errors/403.php';
            exit;
        }

        require_once __DIR__ . '/../views/roles/index.php';
    }

    // ====================================================================
    // API REST METHODS
    // ====================================================================

    /**
     * GET: Obtener un solo rol y sus permisos (para edicion/asignacion)
     * Permiso: ver_roles
     */
    public function getOne()
    {
        $this->checkAuth();

        if (!Permisos::tienePermiso('ver_roles')) {
            $this->jsonResponse(['success' => false, 'message' => 'No tiene permiso para ver roles.'], 403);
        }

        $idRol = isset($_GET['id']) ? sanitizeInt($_GET['id']) : 0;

        if ($idRol <= 0) {
            $this->jsonResponse(["success" => false, "message" => "ID invalido."], 400);
        }

        $rol = $this->roleModel->findById($idRol);
        if (!$rol) {
            $this->jsonResponse(["success" => false, "message" => "Rol no encontrado."], 404);
        }

        // Obtener datos para el modal de permisos
        $permisosDisponibles = $this->permisoModel->getAll();
        $permisosAsignados = $this->permisoModel->getByRol($idRol);

        // Formatear nombres de permisos para lectura humana
        $permisosDisponibles = array_map(function ($permiso) {
            $permiso['nombre_legible'] = ucwords(str_replace('_', ' ', $permiso['nombre']));
            return $permiso;
        }, $permisosDisponibles);

        $this->jsonResponse([
            "success" => true,
            "data" => $rol, // El rol principal
            "permisos" => [
                "disponibles" => $permisosDisponibles,
                "asignados" => array_column($permisosAsignados, 'id')
            ]
        ]);
    }

    /**
     * GET: Listar roles con filtros y paginacion
     * Permiso: ver_roles
     */
    public function getAll()
    {
        $this->checkAuth();

        if (!Permisos::tienePermiso('ver_roles')) {
            $this->jsonResponse(['success' => false, 'message' => 'No tiene permiso para ver roles.'], 403);
        }

        $search = sanitizeInput($_GET['search'] ?? "");
        $sort   = sanitizeInput($_GET['sort'] ?? "id");
        $order  = sanitizeInput($_GET['order'] ?? "ASC");
        $limit  = isset($_GET['limit']) ? sanitizeInt($_GET['limit']) : 10;
        $page   = isset($_GET['page']) ? sanitizeInt($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $allowedSort = ["id", "nombre", "descripcion", "estado"];
        if (!in_array($sort, $allowedSort)) $sort = "id";
        $order = strtoupper($order) === "DESC" ? "DESC" : "ASC";

        try {
            $roles = $this->roleModel->getAll($search, $sort, $order, $limit, $offset);
            $total = $this->roleModel->countAll($search);
            $totalPages = ceil($total / $limit);

            $this->jsonResponse([
                "success" => true,
                "data"    => $roles,
                "pagination" => [
                    "total" => $total,
                    "page" => $page,
                    "limit" => $limit,
                    "pages" => $totalPages
                ]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error al listar roles'], 500);
        }
    }

    /**
     * POST: Crear Rol
     * Permiso: crear_roles
     */
    public function create()
    {
        $this->checkAuth();

        if (!Permisos::tienePermiso('crear_roles')) {
            $this->jsonResponse(["success" => false, "message" => "No tiene permiso para crear roles."], 403);
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $nombre = sanitizeInput($input['nombre'] ?? '');
        $descripcion = sanitizeInput($input['descripcion'] ?? '');

        // Validaciones
        $errors = [];
        if (empty($nombre) || !validateLength($nombre, 50, 3)) {
            $errors['nombre'] = "El nombre debe tener entre 3 y 50 caracteres.";
        }
        if (empty($descripcion) || !validateLength($descripcion, 255, 5)) {
            $errors['descripcion'] = "La descripción debe tener entre 5 y 255 caracteres.";
        }

        if (!empty($errors)) {
            $this->jsonResponse(["success" => false, "message" => "Errores de validación.", "errors" => $errors], 400);
        }

        if ($this->roleModel->create($nombre, $descripcion)) {
            $this->jsonResponse(["success" => true, "message" => "Rol creado correctamente."]);
        } else {
            $this->jsonResponse(["success" => false, "message" => "Error al crear el rol."], 500);
        }
    }

    /**
     * PUT: Actualizar Rol (Datos básicos)
     * Permiso: editar_roles
     */
    public function update()
    {
        $this->checkAuth();

        if (!Permisos::tienePermiso('editar_roles')) {
            $this->jsonResponse(["success" => false, "message" => "No tiene permiso para editar roles."], 403);
        }

        $input = json_decode(file_get_contents("php://input"), true);

        $id = isset($input['id']) ? sanitizeInt($input['id']) : 0;
        $nombre = sanitizeInput($input['nombre'] ?? '');
        $descripcion = sanitizeInput($input['descripcion'] ?? '');
        $estado = sanitizeInput($input['estado'] ?? '');

        if ($id <= 0) {
            $this->jsonResponse(["success" => false, "message" => "Rol no encontrado."], 404);
        }

        $errors = [];
        if (empty($nombre)) $errors['nombre'] = "El nombre es obligatorio.";
        if (!in_array($estado, ['Activo', 'Inactivo'])) $errors['estado'] = "Estado inválido.";

        if (!empty($errors)) {
            $this->jsonResponse(["success" => false, "message" => "Errores de validación.", "errors" => $errors], 400);
        }

        if ($this->roleModel->update($id, $nombre, $descripcion, $estado)) {
            $this->jsonResponse(["success" => true, "message" => "Rol actualizado correctamente."]);
        } else {
            $this->jsonResponse(["success" => false, "message" => "No se pudo actualizar el rol."], 500);
        }
    }

    /**
     * PATCH: Asignar Permisos a un Rol
     * Permiso: asignar_permisos
     */
    public function updatePermissions()
    {
        $this->checkAuth();

        if (!Permisos::tienePermiso('asignar_permisos')) {
            $this->jsonResponse(["success" => false, "message" => "No tiene permiso para asignar permisos."], 403);
        }

        $input = json_decode(file_get_contents("php://input"), true);

        $idRol = isset($input['idRol']) ? sanitizeInt($input['idRol']) : 0;
        $permisos = $input['permisos'] ?? [];

        if ($idRol <= 0) {
            $this->jsonResponse(["success" => false, "message" => "ID de rol inválido."], 400);
        }
        if (!is_array($permisos)) {
            $this->jsonResponse(["success" => false, "message" => "Formato de permisos inválido."], 400);
        }

        // Lógica de negocio: Borrar anteriores y asignar nuevos
        $this->permisoModel->clearByRol($idRol);

        if (!empty($permisos)) {
            $this->permisoModel->assign($idRol, $permisos);
        }

        $this->jsonResponse(["success" => true, "message" => "Permisos actualizados correctamente."]);
    }

    /**
     * DELETE: Eliminar Rol
     * Permiso: eliminar_roles
     */
    public function delete()
    {
        $this->checkAuth();

        if (!Permisos::tienePermiso('eliminar_roles')) {
            $this->jsonResponse(["success" => false, "message" => "No tiene permiso para eliminar roles."], 403);
        }

        $input = json_decode(file_get_contents("php://input"), true);
        $id = isset($input['id']) ? sanitizeInt($input['id']) : 0;

        if ($id <= 0) {
            $this->jsonResponse(["success" => false, "message" => "ID inválido."], 400);
        }

        // Validación de negocio: No borrar si tiene usuarios
        if ($this->roleModel->hasUsers($id)) {
            $this->jsonResponse(["success" => false, "message" => "No se puede eliminar un rol con usuarios asignados."], 409);
        }

        if ($this->roleModel->delete($id)) {
            $this->jsonResponse(["success" => true, "message" => "Rol eliminado correctamente."]);
        } else {
            $this->jsonResponse(["success" => false, "message" => "Error al eliminar el rol."], 500);
        }
    }

    // Helpers Privados
    private function checkAuth()
    {
        if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');
        if (!isUserLoggedIn()) {
            echo json_encode(["success" => false, "message" => "Acceso no autorizado."]);
            http_response_code(401);
            exit;
        }
    }

    private function jsonResponse($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
