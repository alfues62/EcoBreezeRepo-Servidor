<?php
// Incluir el archivo de conexión
require_once '../db/conexion.php'; // Ajusta la ruta según tu proyecto

/**
 * Inserta un nuevo administrador en la base de datos.
 *
 * Este script inserta un nuevo usuario en la tabla `USUARIO` con los datos predeterminados para el administrador.
 * Puedes modificar los siguientes valores para registrar un administrador diferente:
 *   - Nombre: Modifica el valor de la variable `$nombre` para cambiar el nombre del administrador.
 *   - Apellidos: Modifica el valor de la variable `$apellidos` para cambiar los apellidos del administrador.
 *   - Email: Modifica el valor de la variable `$email` para cambiar el correo electrónico del administrador.
 *   - Contraseña: Modifica el valor de la variable `$contrasena` para cambiar la contraseña del administrador.
 *   - Rol: Modifica el valor de la variable `$rol_rolid` para cambiar el rol del administrador.
 *
 * Todos estos valores serán insertados en la base de datos para crear un nuevo administrador.
 * La contraseña será encriptada usando `password_hash` y el usuario será marcado como verificado (`Verificado = 1`).
 *
 * Diseño:
 *
 * Entrada:
 *   - Nombre (string, el nombre del administrador).
 *   - Apellidos (string, los apellidos del administrador).
 *   - Email (string, el correo electrónico del administrador).
 *   - Contrasena (string, la contraseña del administrador).
 *   - Rol (int, el ID del rol del administrador, que por defecto es 1).
 *
 * Proceso:
 *   1. Crear una instancia de la clase `Conexion` para obtener la conexión a la base de datos.
 *   2. Preparar y ejecutar una consulta SQL para insertar el administrador con los valores proporcionados.
 *   3. Si la inserción es exitosa, mostrar un mensaje indicando que el administrador ha sido agregado.
 *   4. Si ocurre un error, capturar la excepción y mostrar el mensaje de error.
 *
 * Salida:
 *   - En caso de éxito:
 *     "Administrador añadido con éxito con ID 1."
 *   - En caso de error:
 *     "Error al insertar el administrador: <mensaje_de_error>"
 *
 * @return void
 */
try {
    // Cambia estos valores para registrar un administrador diferente
    $nombre = "Admin"; // Modifica el nombre aquí
    $apellidos = "Admin"; // Modifica los apellidos aquí
    $email = "admin@admin.com"; // Modifica el correo electrónico aquí
    $contrasena = "admin"; // Modifica la contraseña aquí
    $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT); // Contraseña encriptada
    $rol_rolid = 1; // Cambia el ID del rol si es necesario (por defecto es 1)

    // Crear una instancia de la clase de conexión
    $conexion = new Conexion();
    $conn = $conexion->getConnection();

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
