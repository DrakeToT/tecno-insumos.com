<?php
require_once __DIR__ . '/../config/database.php';

class EquipoModel
{
    private PDO $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Listar equipos con filtros, paginación y relación con categorías
     */
    public function getAll(string $search = '', string $sort = 'id', string $order = 'DESC', int $limit = 10, int $offset = 0): array
    {
        // Campos permitidos para ordenamiento para evitar inyección SQL
        $allowedSort = ['id', 'codigo_inventario', 'categoria', 'marca', 'modelo', 'estado', 'fecha_adquisicion'];
        if (!in_array($sort, $allowedSort)) $sort = 'id';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "
            SELECT 
                e.id, 
                e.codigo_inventario, 
                c.nombre as categoria, 
                e.marca, 
                e.modelo, 
                e.numero_serie, 
                e.estado, 
                e.ubicacion_detalle,
                e.fecha_adquisicion
            FROM equipos e
            INNER JOIN categorias c ON e.id_categoria = c.id
            WHERE (
                :search = '' OR
                e.codigo_inventario LIKE :codigo OR
                e.marca LIKE :marca OR
                e.modelo LIKE :modelo OR
                e.numero_serie LIKE :serial OR
                c.nombre LIKE :nom_cat
            )
            ORDER BY $sort $order
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->conn->prepare($sql);
        $like = "%{$search}%";

        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->bindParam(':codigo', $like, PDO::PARAM_STR);
        $stmt->bindParam(':marca', $like, PDO::PARAM_STR);
        $stmt->bindParam(':modelo', $like, PDO::PARAM_STR);
        $stmt->bindParam(':serial', $like, PDO::PARAM_STR);
        $stmt->bindParam(':nom_cat', $like, PDO::PARAM_STR);
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar total de equipos (para paginación)
     */
    public function countAll(string $search = ''): int
    {
        if ($search === '') {
            $sql = "SELECT COUNT(*) FROM equipos";
            $stmt = $this->conn->query($sql);
            return (int) $stmt->fetchColumn();
        }

        $sql = "
            SELECT COUNT(*) 
            FROM equipos e
            INNER JOIN categorias c ON e.id_categoria = c.id
            WHERE 
                e.codigo_inventario LIKE :codigo OR
                e.marca LIKE :marca OR
                e.modelo LIKE :modelo OR
                e.numero_serie LIKE :serial OR
                c.nombre LIKE :nom_cat
        ";

        $stmt = $this->conn->prepare($sql);
        $like = "%{$search}%";

        $stmt->bindParam(':codigo', $like, PDO::PARAM_STR);
        $stmt->bindParam(':marca', $like, PDO::PARAM_STR);
        $stmt->bindParam(':modelo', $like, PDO::PARAM_STR);
        $stmt->bindParam(':serial', $like, PDO::PARAM_STR);
        $stmt->bindParam(':nom_cat', $like, PDO::PARAM_STR);

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Obtener equipo por ID
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM equipos WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Crear nuevo equipo y retornar su ID
     */
    public function create(array $data): int
    {
        try {
            $sql = "
                INSERT INTO equipos (
                    codigo_inventario, id_categoria, marca, modelo, numero_serie, 
                    estado, ubicacion_detalle, fecha_adquisicion, proveedor, 
                    valor_compra, observaciones
                ) VALUES (
                    :codigo, :id_cat, :marca, :modelo, :serial, 
                    :estado, :ubicacion, :fecha, :proveedor, 
                    :valor, :obs
                )
            ";

            $stmt = $this->conn->prepare($sql);

            // Convertir vacíos a NULL para fechas y números
            $serial = !empty($data['numero_serie']) ? $data['numero_serie'] : null;
            $fecha = !empty($data['fecha_adquisicion']) ? $data['fecha_adquisicion'] : null;
            $valor = !empty($data['valor_compra']) ? $data['valor_compra'] : null;

            $stmt->bindParam(':codigo', $data['codigo_inventario'], PDO::PARAM_STR);
            $stmt->bindParam(':id_cat', $data['id_categoria'], PDO::PARAM_INT);
            $stmt->bindParam(':marca', $data['marca'], PDO::PARAM_STR);
            $stmt->bindParam(':modelo', $data['modelo'], PDO::PARAM_STR);
            $stmt->bindParam(':serial', $serial, PDO::PARAM_STR); // Puede ser null
            $stmt->bindParam(':estado', $data['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':ubicacion', $data['ubicacion_detalle'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR); // Puede ser null
            $stmt->bindParam(':proveedor', $data['proveedor'], PDO::PARAM_STR);
            $stmt->bindParam(':valor', $valor, PDO::PARAM_STR); // Decimal se pasa como string o null
            $stmt->bindParam(':obs', $data['observaciones'], PDO::PARAM_STR);

            if ($stmt->execute()){
                return (int) $this->conn->lastInsertId();
            }
            return 0;

        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Actualizar equipo
     */
    public function update(int $id, array $data): bool
    {
        try {
            $sql = "
                UPDATE equipos SET 
                    codigo_inventario = :codigo,
                    id_categoria = :id_cat,
                    marca = :marca,
                    modelo = :modelo,
                    numero_serie = :serial,
                    estado = :estado,
                    ubicacion_detalle = :ubicacion,
                    fecha_adquisicion = :fecha,
                    proveedor = :proveedor,
                    valor_compra = :valor,
                    observaciones = :obs
                WHERE id = :id
            ";

            $stmt = $this->conn->prepare($sql);

            $serial = !empty($data['numero_serie']) ? $data['numero_serie'] : null;
            $fecha = !empty($data['fecha_adquisicion']) ? $data['fecha_adquisicion'] : null;
            $valor = !empty($data['valor_compra']) ? $data['valor_compra'] : null;

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':codigo', $data['codigo_inventario'], PDO::PARAM_STR);
            $stmt->bindParam(':id_cat', $data['id_categoria'], PDO::PARAM_INT);
            $stmt->bindParam(':marca', $data['marca'], PDO::PARAM_STR);
            $stmt->bindParam(':modelo', $data['modelo'], PDO::PARAM_STR);
            $stmt->bindParam(':serial', $serial, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $data['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':ubicacion', $data['ubicacion_detalle'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
            $stmt->bindParam(':proveedor', $data['proveedor'], PDO::PARAM_STR);
            $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
            $stmt->bindParam(':obs', $data['observaciones'], PDO::PARAM_STR);

            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Validar existencia de código (AJAX)
     */
    public function existeCodigo(string $codigo, ?int $idExcluir = null): bool
    {
        $sql = "SELECT id FROM equipos WHERE codigo_inventario = :codigo";
        if ($idExcluir) {
            $sql .= " AND id != :id";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        
        if ($idExcluir) {
            $stmt->bindParam(':id', $idExcluir, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Validar existencia de número de serie (AJAX)
     */
    public function existeNumeroSerie(string $numeroSerie, ?int $idExcluir = null): bool
    {
        $sql = "SELECT id FROM equipos WHERE numero_serie = :serial";
        if ($idExcluir) {
            $sql .= " AND id != :id";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':serial', $numeroSerie, PDO::PARAM_STR);
        
        if ($idExcluir) {
            $stmt->bindParam(':id', $idExcluir, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }   
    
    /**
     * Eliminar equipo (físico)
     */
    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM equipos WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Probablemente falle si tiene FKs (cuando agreguemos historial)
            return false;
        }
    }
}