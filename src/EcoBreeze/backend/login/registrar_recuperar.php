<?php
require_once '../log.php';
require '../SolicitudCurl.php';
require 'correo_recuperar.php'; // Asegúrate de tener la función enviarCorreoVerificacion en este archivo


function recuperarContraseña($email) {
    $token = bin2hex(random_bytes(16));
        
    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=obtener_datos_usuario_correo';
    
    $data = json_encode([
        'email' => $email,
    ]);

    $result = hacerSolicitudCurl($url, $data);

    if (isset($result['success']) && $result['success']) {
        // Si la API devuelve los datos con éxito, obtenemos el nombre y apellido
        $usuario = $result['usuario'];
        $nombre = $usuario['Nombre'] ?? 'Usuario';
        $apellido = $usuario['Apellidos'] ?? 'Desconocido';
        
        $correoResultado = enviarCorreoRecuperacion($email, $token, $nombre, $apellido);

        if (isset($result['success'])) {
            return ['success' => 'Correo de recuperacion de contraseña enviada'];
        } else {
            return ['error' => 'Error al enviar correo a: ' . $correoResultado];
        }
    } else {
        // Si hubo un error al obtener los datos del usuario, lo registramos
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }
}


?>
