<?php

function enviarCorreoVerificacion($email, $token, $nombre, $apellido) {
    $to = $email;
    $subject = 'Verificación de correo - EcoBreeze';

    // Construcción del mensaje de bienvenida y verificación en formato HTML
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif; color: #000;'>
        <p>Estimado/a $nombre $apellido,</p>

        <p>Gracias por registrarse en <strong>EcoBreeze</strong> y confiar en nosotros para acompañarlo en su experiencia de medición y monitoreo personal. Estamos comprometidos en brindarle un servicio seguro y confiable, y como parte de este proceso, necesitamos confirmar su dirección de correo electrónico.</p>

        <p>Para completar el proceso de verificación y activar su cuenta, por favor siga estos pasos:</p>
        <ol>
            <li>Haga clic en el botón de verificación que encontrará a continuación.</li>
            <li>Una vez verificado su correo electrónico, podrá acceder a su cuenta y comenzar a utilizar todos los servicios que EcoBreeze tiene para ofrecer.</li>
        </ol>

        <p style='text-align: center;'>
            <a href='http://localhost:8080/backend/registrar/verificar_correo.php?email=" . urlencode($email) . "&token=" . urlencode($token) . "' 
               style='display: inline-block; padding: 10px 15px; border-radius: 4px; color: #fff; background-color: #5cb85c; text-decoration: none; transition: background-color 0.3s ease;'>
               Verificar Correo
            </a>
        </p>

        <p>Si el botón anterior no funciona, copie y pegue el siguiente enlace en su navegador:</p>
        <p>
            <a href='http://localhost:8080/backend/registrar/verificar_correo.php?email=" . urlencode($email) . "&token=" . urlencode($token) . "'>
            http://localhost:8080/backend/registrar/verificar_correo.php?email=" . urlencode($email) . "&token=" . urlencode($token) . "
            </a>
        </p>

        <h4>¿Qué sigue después de la verificación?</h4>
        <p>Una vez verificado su correo, podrá iniciar sesión en EcoBreeze y acceder a todas las funciones disponibles. Recuerde que puede configurar la autenticación por huella dactilar en la aplicación móvil para mayor seguridad y comodidad.</p>

        <p>Gracias nuevamente por unirse a EcoBreeze. ¡Estamos emocionados de que forme parte de nuestra comunidad!</p>

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
