<?php
// Inicializa la variable de mensaje
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoge los datos del formulario
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    // Validar que las contraseñas coincidan
    if ($contrasena !== $confirmar_contrasena) {
        $message = "Las contraseñas no coinciden.";
    } else {
        // Hash de la contraseña
        $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

        // Preparar los datos para enviar a la API
        $data = [
            'Nombre' => $nombre,
            'Apellidos' => $apellidos,
            'Email' => $email,
            'ContrasenaHash' => $contrasenaHash,
            'ROL_RolID' => 2
        ];

        // Iniciar una solicitud cURL para enviar los datos a la API
        $ch = curl_init('http://localhost:8080/api_usuario.php'); // Cambia "tu_dominio.com" a tu dominio real
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        // Obtener la respuesta de la API
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Decodificar la respuesta
        $resultado = json_decode($response, true);

        if ($httpcode === 200 && isset($resultado['success'])) {
            $message = $resultado['success'];
        } else {
            $message = isset($resultado['error']) ? $resultado['error'] : 'Error al registrar usuario.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <style>
        body { font-family: Arial, sans-serif; }
        form { max-width: 300px; margin: 20px auto; }
        label { display: block; margin: 5px 0; }
        input { width: 100%; padding: 8px; margin: 5px 0; }
        button { padding: 10px; background-color: #007BFF; color: white; border: none; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Registro de Usuario</h1>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <label for="confirmar_contrasena">Confirmar Contraseña:</label>
        <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
