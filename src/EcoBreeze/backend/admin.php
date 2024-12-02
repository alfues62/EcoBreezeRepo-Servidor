<?php
// Incluir el archivo de conexión
require_once '../db/conexion.php'; // Ajusta la ruta según tu proyecto

try {
    // Crear una instancia de la clase de conexión
    $conexion = new Conexion();
    $conn = $conexion->getConnection();

    // Datos del administrador
    $nombre = "asasdd";
    $apellidos = "asdasdaaasd";
    $email = "das@asdaasdasddASDASDAASDADaasdsadmin.com";
    $contrasena = "asasdad";
    $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);
    $rol_rolid = 2;

    // Query para insertar el administrador con ID forzado
    $query = "INSERT INTO USUARIO 
              ( Nombre, Apellidos, Email, ContrasenaHash, Verificado, token, expiracion_token, token_huella, ROL_RolID) 
              VALUES ( ?, ?, ?, ?, 1, 0, 0, 0, ?)";

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($query);
    $stmt->execute([$nombre, $apellidos, $email, $contrasenaHash, $rol_rolid]);

    // Mensaje de éxito
    echo "Administrador añadido con éxito con ID 1.";
} catch (PDOException $e) {
    // Mensaje de error
    echo "Error al insertar el administrador: " . $e->getMessage();
}
?>
