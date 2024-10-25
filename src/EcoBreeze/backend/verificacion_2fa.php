<?php
session_start(); // Inicia la sesión
require_once 'path/to/GoogleAuthenticator.php'; // Asegúrate de tener la librería

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Redirige al login si no hay sesión
    exit;
}

$conn = (new Conexion())->getConnection(); // Conectar a la base de datos
$ga = new PHPGangsta_GoogleAuthenticator();

// Obtener el usuario
$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT * FROM USUARIO WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Inicializar mensaje
$message = '';

// Verifica si el método de la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo2FA = $_POST['codigo2FA'];

    // Verifica el código
    $resultado = $ga->verifyCode($usuario['TFA_Secret'], $codigo2FA); // Verifica el código

    if ($resultado) {
        // Código correcto, continúa con el inicio de sesión
        $_SESSION['nombre'] = $usuario['Nombre'];
        $_SESSION['rol'] = $usuario['ROL_RolID'];
        header("Location: dashboard.php"); // Redirige al dashboard
        exit;
    } else {
        $message = 'Código 2FA incorrecto.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA</title>
</head>
<body>
    <h1>Verificación 2FA</h1>
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="codigo2FA">Ingresa tu código 2FA</label>
        <input type="text" id="codigo2FA" name="codigo2FA" required>
        <button type="submit">Verificar</button>
    </form>
</body>
</html>
