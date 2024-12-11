<?php
require_once '../log.php';
require_once '../SolicitudCurl.php';
require_once '../validar_token.php';
require_once 'cambiar_correo.php';

// Obtener parámetros de la URL y sanitizarlos
$correoActual = filter_var($_GET['actual'] ?? '', FILTER_SANITIZE_EMAIL);
$nuevoCorreo = filter_var($_GET['nuevo'] ?? '', FILTER_SANITIZE_EMAIL);
$token = htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES, 'UTF-8');

// Inicializar las variables de error y mensaje
$error = '';
$message = '';

// Validar que los parámetros existan
if (empty($correoActual) || empty($nuevoCorreo) || empty($token)) {
    $error = 'Error. Por favor, solicite un nuevo enlace.';
} else {
    // Llamar a la función validarToken
    $resultado = validarToken($correoActual, $token);
    if ($resultado['success']) {
        // Cambiar el correo y obtener el resultado
        $cambioResultado = cambiarCorreo($correoActual, $nuevoCorreo);
        
        if ($cambioResultado['success']) {
            $message = "Correo electrónico cambiado con éxito.";
        } else {
            $error = htmlspecialchars($cambioResultado['error']); // Escapar cualquier entrada para evitar XSS
        }
    } else {
        // Token no válido o error en la verificación
        $error = htmlspecialchars($resultado['error']); // Escapar cualquier entrada para evitar XSS
    }
}

// Incluir la vista para mostrar mensajes y el formulario
include '../../frontend/php/pagina_confirmar_cambio.vista.php';
?>
