
<?php
require_once '../config.php';  // Configuración de la base de datos
include 'registrar_recuperar.php';  // Función de envío de correo

// Inicializamos las variables para los mensajes de éxito y error
$success_message = '';
$error_message = '';

// Comprobamos si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanear y recuperar el correo electrónico del formulario
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

    // Verificar si el correo electrónico es válido
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Llamamos a la función registrarUsuario para procesar el correo
        $resultado = recuperarContraseña($email);

        // Verificamos si el resultado es un mensaje de éxito o un error
        if (isset($resultado['success'])) {
            $success_message = $resultado['success'];
        } else {
            $error_message = $resultado['error'];
        }
    } else {
        // Si el correo no es válido
        $error_message = 'El correo electrónico ingresado no es válido.';
    }
}
?>