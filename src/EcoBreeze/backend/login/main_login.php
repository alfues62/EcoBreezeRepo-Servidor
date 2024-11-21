<?php
session_start();
require '../log.php';
require '../SolicitudCurl.php';
include 'autentificar.php';
include 'registrar_recuperar.php';

// Verificamos si el usuario está logueado, de lo contrario lo redirigimos al login
if (isset($_SESSION['usuario_id'])) {
    // Verificamos el rol del usuario para redirigirlo
    if ($_SESSION['rol'] == 1) {
        // Rol de administrador
        header('Location: ../pagina_admin/main_admin.php');
    } else {
        // Rol de usuario normal
        header('Location: ../pagina_usuario/main_usuario.php');
    }
    exit();
}

$error_message = '';
$success_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $contrasena = trim($_POST['contrasena'] ?? '');
    $action = $_POST['action'] ?? '';

     // Valida el email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Correo electrónico no válido.';
    } else {
        switch ($action) {
            case 'login':
                // Intentar iniciar sesión con el formulario de login
                $result = iniciarSesion($email, $contrasena);

                if (isset($result['error'])) {
                    $error_message = htmlspecialchars($result['error']);
                } else {
                    // Inicia sesión y guarda los datos en la sesión
                    $_SESSION['usuario_id'] = $result['ID'];
                    $_SESSION['nombre'] = $result['Nombre'];
                    $_SESSION['apellidos'] = $result['Apellidos'];
                    $_SESSION['rol'] = $result['Rol'];

                    // Verificamos el rol del usuario para redirigirlo
                    if ($_SESSION['rol'] == 1) {
                        // Rol de administrador
                        header('Location: ../pagina_admin/main_admin.php');
                    } else {
                        // Rol de usuario normal
                        header('Location: ../pagina_usuario/main_usuario.php');
                    }
                    exit();
                }
                break;

            case 'recuperar_contrasena':
                $resultado = registrarRecuperacion($email);

                if (isset($resultado['success'])) {
                    $success_message = $resultado['success'];
                } else {
                    $error_message = $resultado['error'];
                }
                break;

            default:
                $error_message = "Acción desconocida.";
                break;
        }
    }
}

include '../../frontend/php/login.vista.php';
?>
