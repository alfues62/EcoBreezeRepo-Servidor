<?php

require 'enviar_correo.php'; // Asegúrate de tener la función enviarCorreoVerificacion en este archivo


function registrarUsuario($nombre, $apellidos, $email, $contrasena) {
    $token = bin2hex(random_bytes(16));
        
    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=registrar';
    
    $data = json_encode([
        'nombre' => $nombre,
        'apellidos' => $apellidos,
        'email' => $email,
        'contrasena' => $contrasena,
        'token' => $token,
    ]);

    $result = hacerSolicitudCurl($url, $data);

    if (isset($result['success']) && $result['success']) {
        
        // Enviar el correo de verificación
        $correoResultado = enviarCorreoVerificacion($email, $token, $nombre, $apellidos);

        if (strpos($correoResultado, 'success') !== false) {
            return 'Usuario registrado con éxito y correo de verificación enviado.';
        } else {
            return ['error' => 'Usuario registrado con éxito, pero hubo un problema al enviar el correo de verificación: ' . $correoResultado];
        }
    } else {
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }
}
?>
