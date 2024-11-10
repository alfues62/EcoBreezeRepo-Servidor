<?php 
require_once 'log.php';

function enviarCorreoVerificacion($email, $token) {

    $to = $email;

    $subject = 'Verificación de correo';

    $message = 'Haz clic en el siguiente enlace para verificar tu correo: http://localhost:8080/backend/registrar/verificar_correo.php?email=' . urlencode($email) . '&token=' . urlencode($token);

    $headers = 'From: Ecobreeze <gtiproyecto@gmail.com>';


    if (mail($to, $subject, $message, $headers)) {
        registrarError('Correo enviado con éxito!');
        return "success";
    } else {
        registrarError('Error al enviar el correo.');
        return "error";
    }
    
}