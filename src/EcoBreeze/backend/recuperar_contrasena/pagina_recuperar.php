<?php
require_once '../log.php';
require_once '../SolicitudCurl.php';
require_once '../validar_token.php';
require_once 'cambiar_contrasena_recuperar.php';

// Obtener parámetros de la URL y sanitizarlos
$email = filter_var($_GET['email'] ?? '', FILTER_SANITIZE_EMAIL);
$token = htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES, 'UTF-8');

// Inicializar las variables de error y mensaje
$error = '';
$message = '';

// Validar que los parámetros existan
if (empty($email) || empty($token)) {
    $error = 'Error. Por favor, solicite un nuevo enlace.';
} else {
    // Llamar a la función validarToken
    $resultado = validarToken($email, $token);
    if ($resultado['success']) {
        $message = "Código válido. Ahora, ingrese su nueva contraseña.";
    } else {
        // Token no válido o error en la verificación
        $error = htmlspecialchars($resultado['error']); // Escapar cualquier entrada para evitar XSS
    }
}

// Incluir la vista para mostrar mensajes y el formulario
include '../../frontend/php/pagina_recuperar.vista.php';
?>
