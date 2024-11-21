<?php
// Incluye la configuración general del log
include 'config.php';

// Función para registrar errores en el archivo de log
function registrarError($mensaje) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "{$timestamp} - Error: {$mensaje}" . PHP_EOL, FILE_APPEND);
}
?>
