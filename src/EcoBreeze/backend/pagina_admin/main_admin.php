<?php
session_start();

require_once '../SolicitudCurl.php';  // Incluye la clase para hacer la solicitud cURL
require_once '../log.php';  // Incluye el archivo de logging
require_once 'ultima_medicion.php';  // Incluye la función obtenerUltimaMedicion

// Variables para los mensajes de error y éxito
$error_message = '';
$success_message = '';

// Verificamos si el usuario está logueado, de lo contrario lo redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login/main_login.php');
    exit();
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

// Incluir la vista del administrador
include '../../frontend/php/pagina_admin.vista.php';
?>
