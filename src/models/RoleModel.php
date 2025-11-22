<?php
require_once __DIR__ . '/../config/database.php';

class RoleModel
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll(string $search = '', string $sort = 'id', string $order = 'ASC', int $limit = 10, int $offset = 0): array
    {
        $allowedSort = ['id', 'nombre', 'descripcion', 'estado'];
        if (!in_array($sort, $allowedSort)) $sort = 'id';
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $sql =
            "   SELECT id, nombre, descripcion, estado
            FROM roles
            WHERE (
                :search = '' OR
                nombre LIKE :nombre OR
                descripcion LIKE :descripcion
            )
            ORDER BY $sort $order
            LIMIT :limit OFFSET :offset 
        ";

        $stmt = $this->conn->prepare($sql);
        $like = "%{$search}%";

        $stmt->bindValue(':search', $search, PDO::PARAM_STR);
        $stmt->bindValue(':nombre', $like, PDO::PARAM_STR);
        $stmt->bindValue(':descripcion', $like, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear rol
     * El estado se establece automáticamente en la BD (DEFAULT 'Activo')
     */
    public function create($nombre, $descripcion = null)
    {
        $sql = "INSERT INTO roles (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Actualizar rol (incluye estado)
     */
    public function update($id, $nombre, $descripcion, $estado)
    {
        $sql = "
            UPDATE roles
            SET nombre = :nombre,
                descripcion = :descripcion,
                estado = :estado
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Eliminar rol
     */
    public function delete($id)
    {
        $sql = "DELETE FROM roles WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Verificar si existen usuarios con el rol asignado
     */
    public function hasUsers($idRol)
    {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE idRol = :idRol";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idRol', $idRol, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtener un rol por ID
     */
    public function findById($id)
    {
        $sql = "SELECT id, nombre, descripcion, estado FROM roles WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countAll(string $search = ''): int
    {
        $sql = "SELECT COUNT(*) FROM roles WHERE (:search = '' OR nombre LIKE :nombre OR descripcion LIKE :descripcion)";
        $stmt = $this->conn->prepare($sql);

        $like = "%{$search}%";
        $stmt->bindValue(':search', $search, PDO::PARAM_STR);
        $stmt->bindValue(':nombre', $like, PDO::PARAM_STR);
        $stmt->bindValue(':descripcion', $like, PDO::PARAM_STR);

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
