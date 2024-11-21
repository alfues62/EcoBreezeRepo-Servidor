<?php

// Función para cambiar la contraseña por correo
function cambiarContrasenaRecuperar($email, $nuevaContrasena) {
    
    $error_message = '';

    // Definir la URL de la API
    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=cambiar_contrasena_recuperar';

    // Crear el array de datos a enviar, solo con el correo y la nueva contraseña
    $data = json_encode([
        'email' => $email,
        'nueva_contrasena' => $nuevaContrasena
    ]);

    // Realizar la solicitud CURL
    $result = hacerSolicitudCurl($url, $data);
    // Verificar si la respuesta es exitosa
    if (isset($result['success']) && $result['success']) {
        // Retornar mensaje de éxito si la contraseña fue cambiada
        return ['success' => 'Contraseña cambiada con éxito'];
    } else {
        // Si hay un error, registrar el error y retornarlo
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }
}
?>