<?php
require_once '../config.php';
include 'registrar.php';

// Inicializamos las variables para los mensajes de éxito y error
$success_message = '';
$error_message = '';

// Comprobamos si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibimos y sanitizamos los datos del formulario
    $nombre = filter_var(trim($_POST['nombre'] ?? ''), FILTER_SANITIZE_STRING);
    $apellidos = filter_var(trim($_POST['apellidos'] ?? ''), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $contrasena = trim($_POST['contrasena'] ?? '');
    $contrasena_confirmar = trim($_POST['contrasena_confirmar'] ?? '');
    
    
    if ($contrasena !== $contrasena_confirmar) {
        $error_message = 'Las contraseñas no coinciden.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
        $error_message = 'El nombre solo puede contener letras y espacios.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $apellidos)) {
        $error_message = 'Los apellidos solo pueden contener letras y espacios.';
    } 
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $contrasena)) {
        $error_message = 'La contraseña debe tener al menos 8 caracteres, incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.';
    } 
    else {
        // Llamar a la función para registrar el usuario
        $result = registrarUsuario($nombre, $apellidos, $email, $contrasena);

        if (isset($result['error'])) {
            $error_message = htmlspecialchars($result['error']);
        } else {
            $success_message = htmlspecialchars($result);
        }
    }
}

// Incluye la vista de registro con el mensaje de éxito o error
include '../../frontend/php/registro.vista.php';
?>
