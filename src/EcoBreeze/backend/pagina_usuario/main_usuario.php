<?php
session_start();

require_once '../SolicitudCurl.php';
require_once '../log.php';
require_once 'obtener_datos.php';
require_once 'cambiar_contrasena.php';
require_once 'cambiar_correo.php';
require_once 'obtener_mediciones.php';


// Variables para los mensajes de error y éxito
$error_message = '';
$success_message = '';

// Verificamos si el usuario está logueado, de lo contrario lo redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login/main_login.php');
    exit();
}

// Obtener los datos del usuario
if (isset($_SESSION['usuario_id'])) {
    $usuario = obtenerDatosUsuario($_SESSION['usuario_id']);
    if (!$usuario) {
        $error_message = 'Error al obtener los datos del usuario.';
    } else {
        // Obtener las mediciones del usuario logueado
        $mediciones = obtenerMedicionesUsuario($_SESSION['usuario_id']);
        
        // Verificar si la respuesta es válida y contiene mediciones
        if (is_array($mediciones) && !isset($mediciones['error'])) {
            // Si las mediciones están bien, las convertimos a JSON
            $mediciones_json = json_encode($mediciones);
        } else {
            // Si hay un error, lo mostramos
            $error_message2 = 'Error al obtener las mediciones: ' . ($mediciones['error'] ?? 'Datos inválidos.');
            $mediciones_json = '[]'; // Asegura que siempre sea un JSON válido
        }
    }
} else {
    $error_message = 'No estás autenticado. Por favor, inicia sesión.';
    $mediciones_json = '[]'; // Asegura que siempre sea un JSON válido
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
            }elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $nuevaContrasena)) {
                $error_message = 'La contraseña debe tener al menos 8 caracteres, incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.'; 
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

?>
