<?php
class Database {
    // Configuración
    private $host = "localhost";       // Servidor (XAMPP usa localhost)
    private $dbName = "tecnoinsumos";  // Nombre de la base de datos
    private $username = "root";        // Usuario por defecto de XAMPP
    private $password = "";            // XAMPP no usa contraseña por defecto
    private $charset = "utf8mb4";      // Codificación recomendada

    // Conexión
    private $conn;

    // Retorna la conexión activa (PDO)
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // Errores lanzan excepciones
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retorna arrays asociativos
                PDO::ATTR_EMULATE_PREPARES => false               // Evita inyecciones SQL
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Error crítico en la conexión
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }

        return $this->conn;
    }
}
