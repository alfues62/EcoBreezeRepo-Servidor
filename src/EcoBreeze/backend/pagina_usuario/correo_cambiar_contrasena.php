<?php
require_once '../log.php';

function enviarCorreoCambioContrasena($email, $nombre, $apellido) {

    $to = $email;
    $subject = 'Cambio de Contraseña - EcoBreeze';

    // Incluir el nombre y apellido del usuario en el mensaje
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif; color: #000;'>
        <p>Estimado/a $nombre $apellido,</p>

        <p>Queremos informarle que su contraseña en <strong>EcoBreeze</strong> ha sido cambiada exitosamente.</p>

        <p>Si usted no solicitó este cambio, por favor comuníquese con nuestro equipo de soporte inmediatamente.</p>

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
        // Si el correo fue enviado correctamente
        return ['success' => true, 'message' => 'Correo enviado con éxito'];
    } else {
        // Si ocurrió un error al enviar el correo
        $errorInfo = error_get_last();
        $errorMessage = isset($errorInfo['message']) ? $errorInfo['message'] : 'Error desconocido al enviar el correo.';
        registrarError("Error al enviar el correo a $email: $errorMessage");
        return ['success' => false, 'message' => $errorMessage];
    }
}
?>
