<?php
require_once '../log.php';
require_once '../SolicitudCurl.php';
require_once 'validar_token.php';

// Obtener parámetros de la URL
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

$error = '';
$message = '';

// Validar que tanto el email como el token han sido proporcionados
if (empty($email) || empty($token)) {
    $error = 'Error al cargar la URL. Por favor, solicite un nuevo enlace.';
} else {
    // Llamar a la función validarToken para verificar la validez del token
    $resultado = validarToken($email, $token);

    if ($resultado['success']) {
        // Token válido
        $message = "Codigo válido. Ahora ingrese su nueva contraseña.";
    } else {
        // Token no válido o error en la verificación
        $error = $resultado['error'];
    }
}

// Incluir la vista (estructura separada)
include '../../frontend/php/pagina_recuperar.vista.php';
