<?php
require_once 'log.php'; // Incluir la configuración general del log

function enviarCorreoVerificacion($email, $token) {
    registrarError("Iniciando envío de correo a: $email con token: $token");

    $to = $email;
    $subject = 'Verificación de correo';
    $message = 'Haz clic en el siguiente enlace para verificar tu correo: http://localhost:8080/backend/registrar/verificar_correo.php?email=' . urlencode($email) . '&token=' . urlencode($token);
    $headers = 'From: Ecobreeze <gtiproyecto@gmail.com>';

    if (mail($to, $subject, $message, $headers)) {
        registrarError("Correo enviado con éxito a $email.");
        return "success";
    } else {
        // Obtener el último error registrado
        $errorInfo = error_get_last();
        $errorMessage = isset($errorInfo['message']) ? $errorInfo['message'] : 'Error desconocido al enviar el correo.';
        registrarError("Error al enviar el correo a $email: $errorMessage");
        return "error: " . $errorMessage;
    }
}

// Datos de prueba
$email = 'alfues62@gmail.com';
$token = bin2hex(random_bytes(16)); // Generar el token de verificación

// Llamada de prueba
$result = enviarCorreoVerificacion($email, $token);
registrarError("Resultado del envío de correo de prueba: $result");

echo $result;
?>
