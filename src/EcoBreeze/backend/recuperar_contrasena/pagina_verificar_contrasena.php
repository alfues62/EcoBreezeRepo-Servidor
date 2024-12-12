<?php
require_once '../SolicitudCurl.php';  
require_once '../log.php';            
require_once 'cambiar_contrasena_recuperar.php';
require_once '../validar_token.php';

// Obtener los datos del formulario
$email = $_POST['email'] ?? '';
$nuevaContrasena = $_POST['nuevaContrasena'] ?? '';
$token = $_POST['token'] ?? '';

$message = '';
$error = '';

// Verificar si los campos necesarios están presentes
if (empty($email) || empty($nuevaContrasena)) {
    $error = 'Faltan datos para procesar la solicitud.';

} else {
    
    $resultado = validarToken($email, $token);
    if (!$resultado['success']) {
        $error = htmlspecialchars($resultado['error']); // Escapar cualquier entrada para evitar XSS
    } 

    $result = cambiarContrasenaRecuperar($email, $nuevaContrasena);

    // Procesar la respuesta de la API
    if (isset($result['success']) && $result['success']) {
        // Mostrar mensaje de éxito
        $message = htmlspecialchars($result['success']);
    } else {
        // Mostrar mensaje de error si la API falla
        $error = htmlspecialchars($result['error']);
        registrarError($error); // Registrar el error en el log
    }
}

include '../../frontend/php/pagina_verificar_contrasena.vista.php';  // Mostrar la vista con los mensajes
?>
