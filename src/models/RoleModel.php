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

    /**
     * Obtener todos los roles
     */
    public function getAll()
    {
        $sql = "SELECT id, nombre, descripcion, estado FROM roles ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
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
}
