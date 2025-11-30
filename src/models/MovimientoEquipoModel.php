<?php
require_once __DIR__ . '/../config/database.php';

class MovimientoEquipoModel
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Registrar un movimiento en la historia del equipo
     */
    public function registrar(int $idEquipo, int $idUsuario, string $tipo, string $observaciones = ''): bool
    {
        $sql = "INSERT INTO movimientos_equipos (id_equipo, id_usuario, tipo_movimiento, observaciones, fecha) 
                VALUES (:id_equipo, :id_usuario, :tipo, :observaciones, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_equipo', $idEquipo, PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':observaciones', $observaciones, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * Obtener historial completo de un equipo
     */
    public function getByEquipo(int $idEquipo): array
    {
        $sql = "SELECT m.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido 
                FROM movimientos_equipos m 
                JOIN usuarios u ON m.id_usuario = u.id 
                WHERE m.id_equipo = :id 
                ORDER BY m.fecha DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $idEquipo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}