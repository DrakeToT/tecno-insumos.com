<?php
require_once __DIR__ . '/../config/database.php';

class EmpleadoModel {
    private $conn;
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
    public function getAllActive() {
        $stmt = $this->conn->prepare("SELECT id, nombre, apellido, puesto FROM empleados WHERE estado = 'Activo' ORDER BY apellido ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM empleados WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}