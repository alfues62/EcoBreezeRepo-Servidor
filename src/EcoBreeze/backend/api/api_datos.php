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

    default:
        // Maneja métodos no permitidos
        http_response_code(405); // Método no permitido
        error_log("Método no permitido\n", 3, 'C:\UPV\Biometria\EcoBreezeRepo-Servidor\src\projecto\logs\app.log');
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>
