<?php
require_once '../log.php';

function enviarCorreoRecuperacion($email, $token, $nombre, $apellido) {
    $to = $email;
    $subject = 'Recuperación de Contraseña - EcoBreeze';

    // Incluir el nombre y apellido del usuario en el mensaje
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif; color: #000;'>
        <p>Estimado/a $nombre $apellido,</p>

        <p>Hemos recibido una solicitud para recuperar su contraseña en <strong>EcoBreeze</strong>. Para proceder con la recuperación, haga clic en el siguiente enlace:</p>

        <p style='text-align: center;'>
            <a href='http://localhost:8080/backend/recuperar/recuperar_contrasena.php?email=" . urlencode($email) . "&token=" . urlencode($token) . "' 
               style='display: inline-block; padding: 10px 15px; border-radius: 4px; color: #fff; background-color: #5cb85c; text-decoration: none; transition: background-color 0.3s ease;'>
               Recuperar Contraseña
            </a>
        </p>

        <p>Si el botón anterior no funciona, copie y pegue el siguiente enlace en su navegador:</p>
        <p>
            <a href='http://localhost:8080/backend/recuperar/recuperar_contrasena.php?email=" . urlencode($email) . "&token=" . urlencode($token) . "'>
            http://localhost:8080/backend/recuperar/recuperar_contrasena.php?email=" . urlencode($email) . "&token=" . urlencode($token) . "
            </a>
        </p>

        <p>Si no ha solicitado la recuperación de contraseña, ignore este mensaje.</p>

        <p>Saludos cordiales,<br>
        El equipo de EcoBreeze</p>
    </body>
    </html>
    ";

    // Configurar el encabezado para el envío de HTML
    $headers = 'From: EcoBreeze <gtiproyecto@gmail.com>' . "\r\n" .
               'Content-Type: text/html; charset=UTF-8' . "\r\n";

    // Enviar el correo y registrar el resultado en el log
    if (mail($to, $subject, $message, $headers)) {
        registrarError("Correo enviado con éxito a $email.");
        return "success";
    } else {
        $errorInfo = error_get_last();
        $errorMessage = isset($errorInfo['message']) ? $errorInfo['message'] : 'Error desconocido al enviar el correo.';
        registrarError("Error al enviar el correo a $email: $errorMessage");
        return "error: " . $errorMessage;
    }
}
?>
