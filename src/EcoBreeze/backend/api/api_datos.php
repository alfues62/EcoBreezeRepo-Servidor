<?php
require_once(__DIR__ . '/../../db/conexion.php');
require_once(__DIR__ . '/../controllers/datos_CRUD.php');

header('Content-Type: application/json');

// Configurar la zona horaria
date_default_timezone_set('Europe/Madrid');

// Crear una instancia de la clase de conexión
$conn = new Conexion();
$connection = $conn->getConnection();

// Crear una instancia de UsuariosCRUD
$datosCRUD = new DatosCRUD($connection);

// Función para registrar logs
function logMessage($message) {
    $logFile = '/var/www/html/logs/app.log'; // Ruta del archivo log
    file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

// Obtener el valor de 'action' desde la URL usando $_GET
$action = $_GET['action'] ?? null;

// Decodificar el cuerpo JSON para obtener otros parámetros
$requestData = json_decode(file_get_contents('php://input'), true);

switch ($action) {

    case 'obtener_mediciones_usuario':
        // Obtener el ID del usuario desde la solicitud
        $usuario_id = $requestData['usuario_id'] ?? null;
    
        logMessage("ID del usuario: " . $usuario_id);  // Registra el ID del usuario
    
        // Verifica si se proporciona el ID del usuario
        if ($usuario_id) {
            // Llama a la función para obtener las mediciones
            $resultado = $datosCRUD->obtenerMedicionesUsuario($usuario_id);  // Llama a la función de mediciones
            logMessage("Resultado de obtener mediciones: " . print_r($resultado, true)); // Registra el resultado completo
    
            // Imprime la respuesta tal cual, ya que es JSON
            echo $resultado;  // Directamente imprime la respuesta JSON obtenida
        } else {
            // Si no se proporciona el ID del usuario, devolver un error
            echo json_encode(['success' => false, 'error' => 'El ID del usuario es obligatorio.']);
        }
    break;

    case 'obtener_notificaciones_usuario':
        // Obtener el ID del usuario desde la solicitud
        $usuario_id = $requestData['usuario_id'] ?? null;
    
        logMessage("ID del usuario: " . $usuario_id);  // Registra el ID del usuario
    
        // Verifica si se proporciona el ID del usuario
        if ($usuario_id) {
            // Llama a la función para obtener las notificaciones
            $resultado = $datosCRUD->obtenerNotificacionesUsuario($usuario_id);  // Llama a la función de notificaciones
            logMessage("Resultado de obtener notificaciones: " . print_r($resultado, true)); // Registra el resultado completo
    
            // Imprime la respuesta tal cual, ya que es JSON
            echo $resultado;  // Directamente imprime la respuesta JSON obtenida
        } else {
            // Si no se proporciona el ID del usuario, devolver un error
            echo json_encode(['success' => false, 'error' => 'El ID del usuario es obligatorio.']);
        }
    break;

    case 'insertar_notificacion':
        // Obtener los datos de la solicitud
        $usuarioID = $requestData['usuario_id'] ?? null;  // Obtener ID de usuario
        $titulo = $requestData['titulo'] ?? null;         // Obtener título de la notificación
        $cuerpo = $requestData['cuerpo'] ?? null;         // Obtener cuerpo de la notificación
        $fecha = $requestData['fecha'] ?? null;           // Obtener fecha de la notificación
    
        // Registrar los valores recibidos en el log
        logMessage("ID del usuario: " . ($usuarioID ?? "No recibido"));
        logMessage("Título: " . ($titulo ?? "No recibido"));
        logMessage("Cuerpo: " . ($cuerpo ?? "No recibido"));
        logMessage("Fecha: " . ($fecha ?? "No recibido"));
    
        // Verifica si los datos necesarios están presentes
        if (!empty($usuarioID) && !empty($titulo) && !empty($cuerpo) && !empty($fecha)) {
            try {
                // Llamar a la función para insertar la notificación
                $resultado = $datosCRUD->insertarNotificacion($usuarioID, $titulo, $cuerpo, $fecha);
    
                // Manejar la respuesta de la inserción
                if (isset($resultado['success'])) {
                    logMessage("Notificación insertada con éxito: " . json_encode($resultado));
                    echo json_encode(['success' => true, 'message' => $resultado['success']]);
                } else {
                    logMessage("Error al insertar notificación: " . json_encode($resultado));
                    echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al insertar la notificación.']);
                }
            } catch (Exception $e) {
                // Registrar cualquier excepción en los logs
                logMessage("Excepción al insertar notificación: " . $e->getMessage());
                echo json_encode(['success' => false, 'error' => 'Excepción al insertar la notificación: ' . $e->getMessage()]);
            }
        } else {
            // Si falta algún dato necesario, devolver un error
            logMessage("Error: Datos incompletos para insertar la notificación.");
            echo json_encode(['success' => false, 'error' => 'Usuario ID, Título, Cuerpo y Fecha son obligatorios.']);
        }
    break;
    
    default:
        // Maneja métodos no permitidos
        http_response_code(405); // Método no permitido
        error_log("Método no permitido\n", 3, 'C:\UPV\Biometria\EcoBreezeRepo-Servidor\src\projecto\logs\app.log');
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>
