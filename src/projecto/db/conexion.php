<?php 

class Conexion {
    private $conn;

    public function getConnection() {
        // Cargar variables del archivo .env manualmente
        $env = parse_ini_file(__DIR__ . '/../.env'); // Cambia la ruta si es necesario

        try {
            // Conectar a la base de datos utilizando PDO
            $this->conn = new PDO("mysql:host={$env['MYSQL_HOST']};dbname={$env['MYSQL_DATABASE']}", $env['MYSQL_USER'], $env['MYSQL_PASSWORD']);
            // Configurar el modo de error de PDO a excepción
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log('Error de conexión a MySQL: ' . $e->getMessage(), 3, 'logs/app.log');
            die('Error de conexión a MySQL: ' . $e->getMessage());
        }

        return $this->conn;
    }
}
