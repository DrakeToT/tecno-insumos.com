<?php
require_once __DIR__ . '/../config/database.php';

class UserModel
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Buscar usuario por email (para login)
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "
            SELECT 
                u.id, u.nombre, u.apellido, u.email, u.password,
                u.fotoPerfil, u.fechaAlta, u.estado,
                r.id AS idRol, r.nombre AS rol
            FROM usuarios u
            JOIN roles r ON u.idRol = r.id
            WHERE u.email = :email
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Buscar usuario por ID (para perfil u otros usos)
     */
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT 
                u.id, u.nombre, u.apellido, u.email, u.password,
                u.fotoPerfil, u.fechaAlta, u.estado,
                r.id AS idRol, r.nombre AS rol
            FROM usuarios u
            JOIN roles r ON u.idRol = r.id
            WHERE u.id = :id
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Crear nuevo usuario
     */
    public function create(array $data): bool
    {
        $sql = "
            INSERT INTO usuarios (nombre, apellido, email, password, idRol, estado, fechaAlta)
            VALUES (:nombre, :apellido, :email, :password, :idRol, :estado, NOW())
        ";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $data['apellido'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $data['password'], PDO::PARAM_STR);
        $stmt->bindParam(':idRol', $data['idRol'], PDO::PARAM_INT);
        $stmt->bindParam(':estado', $data['estado'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Editar usuario (solo desde panel admin)
     */
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE usuarios 
            SET nombre = :nombre, apellido = :apellido, email = :email, idRol = :idRol, estado = :estado
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':nombre', $data['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $data['apellido'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':idRol', $data['idRol'], PDO::PARAM_INT);
        $stmt->bindParam(':estado', $data['estado'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    /**
     * Actualizar datos personales
     */
    public function updateProfile(int $id, string $nombre, string $apellido, string $email): bool
    {
        $sql = "
            UPDATE usuarios 
            SET nombre = :nombre, apellido = :apellido, email = :email
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword(int $id, string $newHash): bool
    {
        $sql = "UPDATE usuarios SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':password', $newHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Actualizar foto de perfil
     */
    public function updatePhoto(int $id, string $filename): bool
    {
        $sql = "UPDATE usuarios SET fotoPerfil = :foto WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':foto', $filename, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Cambiar estado (Activo/Inactivo)
     */
    public function changeStatus(int $id, string $nuevoEstado): bool
    {
        $sql = "UPDATE usuarios SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':estado', $nuevoEstado, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Listar usuarios (con filtro opcional)
     */
    public function getAll(string $search = '', string $sort = 'id', string $order = 'ASC', int $limit = 10, int $offset = 0): array
    {
        $allowedSort = ['id', 'nombre', 'apellido', 'email', 'estado', 'rol'];
        if (!in_array($sort, $allowedSort)) $sort = 'id';
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "
            SELECT 
                u.id, u.nombre, u.apellido, u.email, r.nombre AS rol, u.estado
            FROM usuarios u
            JOIN roles r ON u.idRol = r.id
            WHERE (
                :search = '' OR
                u.nombre LIKE :nombre OR
                u.apellido LIKE :apellido OR
                u.email LIKE :email
            )
            ORDER BY $sort $order
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->conn->prepare($sql);
        $like = "%{$search}%";

        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $like, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $like, PDO::PARAM_STR);
        $stmt->bindParam(':email', $like, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar usuarios activos
     */
    public function getAllActive(): array
    {
        $sql = "
            SELECT 
                u.id, u.nombre, u.apellido, u.email, r.nombre AS rol, u.estado
            FROM usuarios u
            JOIN roles r ON u.idRol = r.id
            WHERE u.estado = 'Activo'
            ORDER BY u.id ASC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cantidad de usuarios por consultar
     */
    public function countAll(string $search = ''): int
    {
        // Consulta del total de usuarios cuando el parámetro searche es vacio
        if ($search === '') {
            $sql = "SELECT COUNT(*) FROM usuarios";
            $stmt = $this->conn->query($sql);
            return (int) $stmt->fetchColumn();
        }

        // Consulta las coincidencias según el parámetro search 
        $sql = "
            SELECT COUNT(*) 
            FROM usuarios 
            WHERE nombre LIKE :nombre 
                OR apellido LIKE :apellido 
                OR email LIKE :email
        ";
        $stmt = $this->conn->prepare($sql);
        $like = "%{$search}%";
        $stmt->bindParam(':nombre', $like, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $like, PDO::PARAM_STR);
        $stmt->bindParam(':email', $like, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Eliminar usuario
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
