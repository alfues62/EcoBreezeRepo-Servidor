<?php
session_start(); // Inicia la sesión para manejar la autenticación
$message = '';

// Verifica si el método de la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos
    require_once(__DIR__ . '/controllers/usuario_CRUD.php');
    require_once(__DIR__ . '/../db/conexion.php'); // Asegúrate de incluir la conexión
    $conn = (new Conexion())->getConnection(); // Obtiene la conexión a la base de datos
    $usuariosCRUD = new UsuariosCRUD($conn); // Pasa la conexión al CRUD

    // Recoge los datos del formulario
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    // Busca el usuario en la base de datos
    $usuario = $usuariosCRUD->leer(null, null, null, $email);

    // Verifica si el usuario existe
    if (!empty($usuario)) {
        $usuario = $usuario[0]; // Toma el primer resultado

        // Verifica la contraseña
        if (password_verify($contrasena, $usuario['ContrasenaHash'])) {
            // Si la contraseña es correcta, guarda la información en la sesión
            $_SESSION['usuario_id'] = $usuario['ID'];
            $_SESSION['nombre'] = $usuario['Nombre'];
            $_SESSION['rol'] = $usuario['ROL_RolID']; // Guarda el rol si es necesario

            header("Location: dashboard.php"); // Redirige a la página de inicio después del login
            exit;
        } else {
            $message = 'Contraseña incorrecta.';
        }
    } else {
        $message = 'Usuario no encontrado.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p> <!-- Escapa el mensaje para mayor seguridad -->
    <?php endif; ?>

    <form action="" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br>

        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>
