<?php
session_start();
require_once '../config.php';
include 'autentificar.php';

// Verificamos si el usuario está logueado, de lo contrario lo redirigimos al login
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../pagina_usuario/main_usuario.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $contrasena = trim($_POST['contrasena'] ?? '');

    // Valida el email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Correo electrónico no válido.';
    } else {
        // Intenta iniciar sesión
        $result = iniciarSesion($email, $contrasena);

        if (isset($result['error'])) {
            $error_message = htmlspecialchars($result['error']);
        } else {
            // Inicia sesión y redirige al usuario
            $_SESSION['usuario_id'] = $result['ID'];
            $_SESSION['nombre'] = $result['Nombre'];
            $_SESSION['rol'] = $result['Rol'];

            header('Location: /backend/pagina_usuario/main_usuario.php');
            exit();
        }
    }
}

// Incluye la vista de login con el mensaje de error
include '../../frontend/php/login.vista.php';
?>
