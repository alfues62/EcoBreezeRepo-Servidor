<?php
/**
 * Registra errores en el archivo de log.
 *
 * Este script permite registrar errores o mensajes personalizados en un archivo de log. 
 * Asegúrate de configurar correctamente el archivo de log y los permisos de escritura antes de usar este script.
 *
 * Para personalizar la configuración de este log, modifica los siguientes valores en el archivo `config.php`:
 *   - `$logFile`: Modifica la ruta y el nombre del archivo donde se guardarán los registros de errores. 
 *     Ejemplo: `$logFile = '/path/to/your/logfile.log';`.
 *
 * Función `registrarError`:
 *   - Recibe un mensaje de error como parámetro y lo guarda en el archivo de log.
 *   - Registra un timestamp con la fecha y hora exacta de cuando se produjo el error.
 *   - El mensaje se añadirá al final del archivo de log, sin sobrescribir entradas previas.
 *
 * Diseño:
 *
 * Entrada:
 *   - $mensaje (string): El mensaje de error o información que se desea registrar en el archivo de log.
 *
 * Proceso:
 *   1. Obtener la fecha y hora actual utilizando `date()`.
 *   2. Escribir el mensaje de error en el archivo de log junto con el timestamp.
 *   3. El archivo de log se actualizará sin sobrescribir los registros anteriores.
 *
 * Salida:
 *   - No hay salida directamente visible. Los mensajes de error se guardan en el archivo de log.
 *
 * @param string $mensaje El mensaje que se desea registrar en el log.
 * @return void
 */

// Incluye la configuración general del log
include 'config.php'; // Asegúrate de modificar la ruta de `config.php` según la ubicación del archivo

// Función para registrar errores en el archivo de log
function registrarError($mensaje) {
    global $logFile; // Usamos la variable global `$logFile` definida en `config.php`
    $timestamp = date('Y-m-d H:i:s'); // Formato de fecha y hora: Año-Mes-Día Hora:Minuto:Segundo
    file_put_contents($logFile, "{$timestamp} - {$mensaje}" . PHP_EOL, FILE_APPEND); // Añade el mensaje al final del archivo
}

?>
