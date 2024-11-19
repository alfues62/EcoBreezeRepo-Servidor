<?php
session_start();
ob_start(); // Inicia el buffer de salida

include '../config.php';
include 'ver_datos.php';
include 'cambiar_contrasena.php';
include 'cambiar_correo.php';

// Variables para los mensajes de error y éxito
$error_message = '';
$success_message = '';

// Verificamos si el usuario está logueado, de lo contrario lo redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login/main_login.php');
    exit();
}

// Obtener los datos del usuario
$usuario = obtenerDatosUsuario($_SESSION['usuario_id']);
if (!$usuario) {
    $error_message = 'Error al obtener los datos del usuario.';
}

// Comprobamos si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtenemos el formulario
    $action = $_POST['action'] ?? '';

    // Usamos un switch para manejar las acciones de los formularios
    switch ($action) {
        case 'cambiar_contrasena':
            $id = $_SESSION['usuario_id'] ?? null;
            $contrasenaActual = trim($_POST['contrasena_actual'] ?? '');
            $nuevaContrasena = trim($_POST['nueva_contrasena'] ?? '');
            $confirmarContrasena = trim($_POST['confirmar_contrasena'] ?? '');

            // Verificar que las nuevas contraseñas coincidan
            if ($nuevaContrasena !== $confirmarContrasena) {
                $error_message = 'Las nuevas contraseñas no coinciden.';
            } else {
                // Cambiar la contraseña y obtener el resultado
                $result = cambiarContrasena($id, $contrasenaActual, $nuevaContrasena);

                // Comprobar si la respuesta es de éxito
                if (isset($result['success'])) {
                    $success_message = $result['success'];  // Mensaje de éxito
                } else {
                    $error_message = $result['error'];  // Mensaje de error
                }
            }
            break;

        case 'cambiar_correo':
            $id = $_SESSION['usuario_id'] ?? null;
            $nuevoCorreo = trim($_POST['email'] ?? '');
            $contrasenaActual = trim($_POST['contrasena_actual_correo'] ?? '');

            if ($id) {
                // Cambiar el correo y obtener el resultado
                $result = cambiarCorreo($id, $contrasenaActual, $nuevoCorreo);

                if (isset($result['success'])) {
                    $success_message = $result['success'];
                } else {
                    $error_message = $result['error'];
                }
            } else {
                $error_message = 'No estás autenticado. Por favor, inicia sesión.';
            }
            break;

        default:
            $error_message = 'Acción no válida.';
            break;
    }
}

// Incluye la vista de la página de usuario
include '../../frontend/php/pagina_usuario.vista.php';

ob_end_flush(); // Envía el contenido del buffer de salida y desactiva el buffer
?>
