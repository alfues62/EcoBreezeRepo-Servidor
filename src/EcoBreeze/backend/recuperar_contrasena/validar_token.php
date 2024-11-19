<?php

function validarToken($email, $token) {
    $token = bin2hex(random_bytes(16));
        
    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=registrar';
    
    $data = json_encode([
        'email' => $email,
        'token_verficicacion' => $token,
    ]);

    $result = hacerSolicitudCurl($url, $data);

    if (isset($result['success']) && $result['success']) {
        
    }
}
?>
