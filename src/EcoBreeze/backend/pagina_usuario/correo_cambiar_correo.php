<?php

function enviarCorreoCambio($email, $nuevoCorreo, $token, $nombre, $apellido) {
    $to = $nuevoCorreo; // Enviar al nuevo correo
    $subject = 'Cambio de correo electrónico - EcoBreeze';

    // Construcción del mensaje de notificación de cambio de correo en formato HTML
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif; color: #000;'>
        <p>Estimado/a $nombre $apellido,</p>

        <p>Hemos recibido una solicitud para cambiar su dirección de correo electrónico en <strong>EcoBreeze</strong>.</p>

        <p>Para confirmar el cambio, por favor siga estos pasos:</p>
        <ol>
            <li>Haga clic en el botón de verificación que encontrará a continuación.</li>
            <li>Una vez verificado el nuevo correo electrónico, se actualizará su cuenta.</li>
        </ol>

        <p style='text-align: center;'>
            <a href='http://localhost:8080/backend/pagina_usuario/confirmar_cambio.php?actual=" . urlencode($email) . "&nuevo=" . urlencode($nuevoCorreo) . "&token=" . urlencode($token) . "' 
               style='display: inline-block; padding: 10px 15px; border-radius: 4px; color: #fff; background-color: #5cb85c; text-decoration: none; transition: background-color 0.3s ease;'>
               Verificar Cambio de Correo
            </a>
        </p>

        <p>Si el botón anterior no funciona, copie y pegue el siguiente enlace en su navegador:</p>
        <p>
            <a href='http://localhost:8080/backend/pagina_usuario/confirmar_cambio.php?actual=" . urlencode($email) . "&nuevo=" . urlencode($nuevoCorreo) . "&token=" . urlencode($token) . "'>
            http://localhost:8080/backend/pagina_usuario/confirmar_cambio.php?actual=" . urlencode($email) . "&nuevo=" . urlencode($nuevoCorreo) . "&token=" . urlencode($token) . "
            </a>
        </p>

        <p>Si usted no solicitó este cambio, por favor comuníquese con nuestro equipo de soporte inmediatamente.</p>

        <p>Saludos cordiales,<br>
        El equipo de EcoBreeze</p>
    </body>
    </html>
    ";

    // Configurar el encabezado para envío de HTML
    $headers = 'From: EcoBreeze <gtiproyecto@gmail.com>' . "\r\n" .
               'Content-Type: text/html; charset=UTF-8' . "\r\n";

    // Enviar el correo y registrar el resultado en el log
    if (mail($to, $subject, $message, $headers)) {
        return "success";
    } else {
        $errorInfo = error_get_last();
        $errorMessage = isset($errorInfo['message']) ? $errorInfo['message'] : 'Error desconocido al enviar el correo.';
        registrarError("Error al enviar el correo a $email: $errorMessage");
        return "error: " . $errorMessage;
    }
}
