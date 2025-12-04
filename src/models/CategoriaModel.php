<?php
require_once __DIR__ . '/../config/database.php';

class CategoriaModel
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Obtiene todas las categorías activas para llenar dropdowns (uso en formularios)
     */
    public function getAllActive(): array
    {
        $sql = "SELECT id, nombre FROM categorias WHERE estado = 'Activo' ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las categorías (gestión administrativa)
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM categorias ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una categoría activa por ID
     */
    public function getActiveById(int $id): ?array
    {
        $sql = "SELECT * FROM categorias WHERE id = :id AND estado = 'Activo' LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    /**
     * Obtiene una categoría por ID (útil para validaciones)
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM categorias WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}