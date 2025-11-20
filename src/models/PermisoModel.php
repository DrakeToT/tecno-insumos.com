<?php

require_once __DIR__ . '/../config/database.php';

class PermisoModel
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Obtener todos los permisos
     */
    public function getAll(): array
    {
        $sql = "SELECT id, nombre, descripcion FROM permisos ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener permisos de un rol
     */
    public function getByRol(int $idRol): array
    {
        $sql = "
            SELECT p.id, p.nombre, p.descripcion
            FROM permisos p
            INNER JOIN rolesPermisos rp ON rp.idPermiso = p.id
            WHERE rp.idRol = :idRol
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idRol', $idRol, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Borrar permisos asignados a un rol
     */
    public function clearByRol(int $idRol): bool
    {
        $sql = "DELETE FROM rolesPermisos WHERE idRol = :idRol";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idRol', $idRol, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Asignar permisos a un rol
     */
    public function assign(int $idRol, array $permisosId): bool
    {
        $sql = "INSERT INTO rolesPermisos (idRol, idPermiso) VALUES (:idRol, :idPermiso)";
        $stmt = $this->conn->prepare($sql);

        foreach ($permisosId as $permisoId) {
            $stmt->execute([
                ':idRol'     => $idRol,
                ':idPermiso' => $permisoId
            ]);
        }

        return true;
    }
}
