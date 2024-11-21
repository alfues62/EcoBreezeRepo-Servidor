<?php
// Incluir el archivo de conexión
require_once '../db/conexion.php'; // Ajusta la ruta según tu proyecto

try {
    // Crear una instancia de la clase de conexión
    $conexion = new Conexion();
    $conn = $conexion->getConnection();

    // Datos del administrador
    $id = 1; // Forzar el ID a 1
    $nombre = "Admin";
    $apellidos = "Admin";
    $email = "admin@admin.com";
    $contrasena = "admin";
    $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);
    $rol_rolid = 1;

    // Query para insertar el administrador con ID forzado
    $query = "INSERT INTO USUARIO 
              (ID, Nombre, Apellidos, Email, ContrasenaHash, Verificado, token, expiracion_token, token_huella, ROL_RolID) 
              VALUES (?, ?, ?, ?, ?, 1, 0, 0, 0, ?)";

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($query);
    $stmt->execute([$id, $nombre, $apellidos, $email, $contrasenaHash, $rol_rolid]);

    // Mensaje de éxito
    echo "Administrador añadido con éxito con ID 1.";
} catch (PDOException $e) {
    // Mensaje de error
    echo "Error al insertar el administrador: " . $e->getMessage();
}
?>
