<?php
session_start();

require_once '../SolicitudCurl.php';
require_once '../log.php';
require_once 'obtener_datos.php';
require_once 'cambiar_contrasena.php';
require_once 'actualizar_token_correo.php';
require_once 'obtener_mediciones.php';
require_once 'correo_cambiar_contrasena.php';
require_once 'correo_cambiar_correo.php';

// Variables globales de sesión
$usuario_id = $_SESSION['usuario_id'] ?? null;
$nombre = $_SESSION['nombre'] ?? null;
$apellidos = $_SESSION['apellidos'] ?? null;
$email = $_SESSION['email'] ?? null;

// Redirigir si el usuario no está logueado
if (!$usuario_id) {
    header('Location: ../login/main_login.php');
    exit();
}

// Inicialización de variables
$error_message = '';
$success_message = '';

// Obtener los datos del usuario
$usuario = obtenerDatosUsuario($usuario_id);
if (!$usuario) {
    $error_message = 'Error al obtener los datos del usuario.';
} else {
    // Obtener las mediciones del usuario logueado
    $mediciones = obtenerMedicionesUsuario($usuario_id);
    $mediciones_json = (is_array($mediciones) && !isset($mediciones['error'])) ? json_encode($mediciones) : '[]';
}

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'cambiar_contrasena':
            $contrasenaActual = trim($_POST['contrasena_actual'] ?? '');
            $nuevaContrasena = trim($_POST['nueva_contrasena'] ?? '');
            $confirmarContrasena = trim($_POST['confirmar_contrasena'] ?? '');

            // Verificar que las contraseñas coincidan y tengan formato válido
            if ($nuevaContrasena !== $confirmarContrasena) {
                $error_message = 'Las nuevas contraseñas no coinciden.';
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $nuevaContrasena)) {
                $error_message = 'La contraseña debe tener al menos 8 caracteres, incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.';
            } else {
                // Cambiar la contraseña
                $result = cambiarContrasena($usuario_id, $contrasenaActual, $nuevaContrasena);
                if (isset($result['success'])) {
                    enviarCorreoCambioContrasena($email, $nombre, $apellidos);
                    $success_message = $result['success'];
                } else {
                    $error_message = $result['error'];
                }
            }
            break;

        case 'cambiar_correo':
            $nuevoCorreo = trim($_POST['nuevo_email'] ?? '');
            $contrasenaActual = trim($_POST['contrasena_actual'] ?? '');

            if ($usuario_id) {
                $token = bin2hex(random_bytes(32)); // Generar token
                $result = cambiarToken($usuario_id, $contrasenaActual, $nuevoCorreo, $token);

                if (isset($result['success'])) {
                    enviarCorreoCambio($email, $nuevoCorreo, $token, $nombre, $apellidos);
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

// Incluir la vista
include '../../frontend/php/inicio_usuario.vista.php';
?>
