<?php
session_start();

require_once '../SolicitudCurl.php';  // Incluye la clase para hacer la solicitud cURL
require_once '../log.php';  // Incluye el archivo de logging
require_once 'ultima_medicion.php';  // Incluye la función obtenerUltimaMedicion
require_once 'eliminar_usuario.php';  // Incluye la función eliminarUsuario
require_once 'obtener_todos_datos.php';  

// Variables para los mensajes de error y éxito
$error_message = '';
$success_message = '';

// Verificamos si el usuario está logueado, de lo contrario lo redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login/main_login.php');
    exit();
}

// Manejar la solicitud de eliminación de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'eliminar_usuario') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $resultado = eliminarUsuario($id);
        if (isset($resultado['success'])) {
            $success_message = $resultado['success'];
        } else {
            $error_message = $resultado['error'];
        }
    } else {
        $error_message = 'El ID del usuario es obligatorio.';
    }
}

$result = obtenerUltimaMedicion();


// Comprobamos si hubo un error en la respuesta
if ($result === false) {
    // Si la solicitud cURL falló, mostramos un mensaje de error
    $error_message = 'Hubo un problema al obtener los datos de la API.';
} else {
    // Verificamos si la respuesta contiene los usuarios directamente
    if (isset($result['usuarios']) && is_array($result['usuarios']) && count($result['usuarios']) > 0) {
        $usuarios = $result['usuarios'];  // Asignamos directamente los usuarios
    } else {
        // Si no hay usuarios o la respuesta está vacía
        $error_message = 'No se encontraron usuarios con mediciones.';
    }
}

$datosMapa = obtenerMediciones();


// Incluir la vista del administrador
include '../../frontend/php/pagina_admin.vista.php';
?>
