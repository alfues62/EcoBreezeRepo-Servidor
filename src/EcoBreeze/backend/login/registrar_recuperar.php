<?php

require_once '../recuperar_contrasena/correo_recuperar.php';

function registrarRecuperacion($email) {
    $token = bin2hex(random_bytes(32));
    

    // Define la URL del endpoint de la API
    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=actualizar_token_recuperacion';

    // Prepara los datos a enviar
    $data = json_encode([
        'email' => $email,
        'token' => $token
    ]);
    // Realiza la solicitud cURL
    $result = hacerSolicitudCurl($url, $data);

    // Verifica si la respuesta contiene éxito
if (isset($result['success']) && $result['success']) {
    $usuario = $result['usuario'] ?? null; // Extraemos 'usuario'

    if ($usuario && isset($usuario['nombre'], $usuario['apellidos'], $usuario['email'])) {
        // Llamamos a la función para enviar el correo de recuperación
        $resultadoCorreo = enviarCorreoRecuperacion($usuario['email'], $token, $usuario['nombre'], $usuario['apellidos']);

        if ($resultadoCorreo['success']) {
            return ['success' => 'Correo enviado exitosamente'];
        } else {
            // Registrar error del correo y devolver un mensaje
            $error_message = "Token actualizado, pero ocurrió un error al enviar el correo: " . $resultadoCorreo['message'];
            registrarError($error_message);
            return ['error' => $error_message];
        }
    } else {
        // Si la respuesta no contiene la información necesaria, registra un error
        $error_message = 'Error en la estructura de respuesta de la API al actualizar el token.';
        registrarError($error_message);
        return ['error' => $error_message];
    }
} else {
    // Si la respuesta no fue exitosa, registra el error y devuelve un mensaje de error
    $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
    registrarError($error_message);
    return ['error' => $error_message];
}

    
}
?>