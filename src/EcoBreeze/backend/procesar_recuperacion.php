<?php
// Conectar a la base de datos
include_once '../db/conexion.php';
$db = new Conexion();
$conn = $db->getConnection();

// Recoge el correo electrónico ingresado
$email = $_POST['email'];

// Verifica si el correo está en la base de datos
$sql = "SELECT * FROM USUARIO WHERE Email = :email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    // Genera el token de recuperación
    $token = bin2hex(random_bytes(16));
    $url_recuperacion = "http://localhost:8080/restablecer_contrasena.php?token=" . $token;
    
    // Guarda el token en la base de datos con una fecha de expiración (ejemplo: 1 hora)
    $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $sql_token = "UPDATE USUARIO SET token_recuperacion = :token, expiracion_token = :expiracion WHERE Email = :email";
    $stmt_token = $conn->prepare($sql_token);
    $stmt_token->bindParam(':token', $token);
    $stmt_token->bindParam(':expiracion', $expiracion);
    $stmt_token->bindParam(':email', $email);
    $stmt_token->execute();

    // Envía el correo con el enlace de recuperación
    $to = $email;
    $subject = "Recuperación de Contraseña";
    $message = "Haz clic en el siguiente enlace para restablecer tu contraseña: " . $url_recuperacion;
    $headers = "From: no-reply@tu_dominio.com";

    mail($to, $subject, $message, $headers);
    echo "Se ha enviado un enlace de recuperación a tu correo.";
} else {
    echo "Correo no registrado.";
}
?>
