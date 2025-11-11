<?php
require_once __DIR__ . '/../config/database.php';

class RoleModel
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // =====================================================
    // Obtener todos los roles
    // =====================================================
    public function getAll()
    {
        $sql = "SELECT id, nombre, descripcion FROM roles ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // Crear nuevo rol
    // =====================================================
    public function create($nombre, $descripcion = null)
    {
        $sql = "INSERT INTO roles (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':descripcion', $descripcion);
        return $stmt->execute();
    }

    // =====================================================
    // Actualizar rol existente
    // =====================================================
    public function update($id, $nombre, $descripcion = null)
    {
        $sql = "UPDATE roles SET nombre = :nombre, descripcion = :descripcion WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':descripcion', $descripcion);
        return $stmt->execute();
    }

    // =====================================================
    // Eliminar rol
    // =====================================================
    public function delete($id)
    {
        $sql = "DELETE FROM roles WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // =====================================================
    // Verificar si el rol tiene usuarios asignados
    // =====================================================
    public function hasUsers($idRol)
    {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE idRol = :idRol";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idRol', $idRol, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    // =====================================================
    // Obtener un rol por ID (útil para editar)
    // =====================================================
    public function findById($id)
    {
        $sql = "SELECT id, nombre, descripcion FROM roles WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
