<?php
// db.php
$dotenv = parse_ini_file('/var/www/html/db/.env');  // Carga el archivo .env

// Verifica si se cargaron las variables correctamente
if ($dotenv === false) {
    die("Error al cargar el archivo .env");
}

// Extrae las variables
$mysql_host = 'mysql-db';  // Asegúrate de que este sea el nombre correcto del contenedor de MySQL
$mysql_user = $dotenv['MYSQL_USER'];
$mysql_password = $dotenv['MYSQL_PASSWORD'];
$mysql_database = $dotenv['MYSQL_DATABASE'];

// Intenta establecer la conexión
try {
    $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
