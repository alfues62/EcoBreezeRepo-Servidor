<?php
require_once '../log.php';

function iniciarSesion($email, $contrasena) {

    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=iniciar_sesion';
    $data = json_encode([
        'email' => $email,
        'contrasena' => $contrasena
    ]);

    $result = hacerSolicitudCurl($url, $data);

    if (isset($result['success']) && $result['success']) {
        $usuario = $result['usuario'] ?? null;
        if ($usuario && isset($usuario['ID'], $usuario['Nombre'], $usuario['Rol'])) {
            return [
                'ID' => $usuario['ID'], 
                'Nombre' => $usuario['Nombre'], 
                'Rol' => $usuario['Rol']
            ];
        } else {
            $error_message = 'Error en la estructura de respuesta de la API.';
            registrarError($error_message);
            return ['error' => $error_message];
        }
    } else {
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }
    
}
?>
