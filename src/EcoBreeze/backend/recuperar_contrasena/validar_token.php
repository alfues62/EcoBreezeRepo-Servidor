<?php

function validarToken($email, $token) {        
    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=verificar_token';
    
    $data = json_encode([
        'email' => $email,
        'token' => $token,
    ]);

    // Hacer la solicitud CURL
    $result = hacerSolicitudCurl($url, $data);

    // Verificar el resultado y devolver una respuesta
    if (isset($result['success']) && $result['success']) {
        return [
            'success' => true,
            'message' => 'El token es válido y ha sido verificado correctamente.'
        ];
    } else {
        // Manejo de errores si la verificación falla
        $error = $result['error'] ?? 'Error desconocido durante la verificación.';
        return [
            'success' => false,
            'error' => $error
        ];
    }
}
?>
