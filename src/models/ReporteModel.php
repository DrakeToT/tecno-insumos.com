<?php
require_once __DIR__ . '/../config/database.php';

class ReporteModel
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Obtiene la información básica de la entidad (Usuario, Empleado o Área)
     */
    public function getDatosEntidad(string $tipo, int $id): ?array
    {
        $sql = "";
        
        switch ($tipo) {
            case 'usuario':
                $sql = "SELECT id, CONCAT(nombre, ' ', apellido) as nombre_completo, email as dato_extra, 'Usuario del Sistema' as tipo_entidad 
                        FROM usuarios WHERE id = :id";
                break;
            case 'empleado':
                $sql = "SELECT id, CONCAT(nombre, ' ', apellido) as nombre_completo, puesto as dato_extra, 'Empleado' as tipo_entidad 
                        FROM empleados WHERE id = :id";
                break;
            case 'area':
                $sql = "SELECT id, nombre as nombre_completo, descripcion as dato_extra, 'Área' as tipo_entidad 
                        FROM areas WHERE id = :id";
                break;
            default:
                return null;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Obtiene los equipos asignados actualmente a esa entidad
     */
    public function getEquiposAsignados(string $tipo, int $id): array
    {
        $sql = "
            SELECT 
                e.id,
                e.codigo_inventario,
                c.nombre as categoria,
                e.marca,
                e.modelo,
                e.numero_serie,
                e.estado,
                e.fecha_adquisicion,
                e.valor_compra
            FROM equipos e
            JOIN categorias c ON e.id_categoria = c.id
            WHERE e.asignado_tipo = :tipo 
              AND e.asignado_id = :id
              AND e.estado = 'Asignado'
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}