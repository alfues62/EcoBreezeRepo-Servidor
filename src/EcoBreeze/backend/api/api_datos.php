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

    
    case 'GET':
        // Maneja la solicitud GET
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : null;
        $apellidos = isset($_GET['apellidos']) ? $_GET['apellidos'] : null;
        $email = isset($_GET['email']) ? $_GET['email'] : null;
        
        $usuarios = $usuariosCRUD->leer($id, $nombre, $apellidos, $email);
        echo json_encode($usuarios);
        break;

    case 'POST':
        // Maneja la solicitud POST
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['Nombre'], $input['Apellidos'], $input['Email'], $input['ContrasenaHash'], $input['ROL_RolID'])) {
            $nombre = $input['Nombre'];
            $apellidos = $input['Apellidos'];
            $email = $input['Email'];
            $contrasenaHash = $input['ContrasenaHash'];
            $rol_rolid = $input['ROL_RolID'];
            $tfa_secret = isset($input['TFA_Secret']) ? $input['TFA_Secret'] : null;

            $resultado = $usuariosCRUD->insertar($nombre, $apellidos, $email, $contrasenaHash, $rol_rolid, $tfa_secret);
            echo json_encode($resultado);
        } else {
            error_log("Datos incompletos para insertar el usuario.\n", 3, 'C:\UPV\Biometria\EcoBreezeRepo-Servidor\src\projecto\logs\app.log');
            echo json_encode(['error' => 'Datos incompletos para insertar el usuario.']);
        }
        break;

    case 'PUT':
        // Maneja la solicitud PUT
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['ID'], $input['Nombre'], $input['Apellidos'], $input['Email'], $input['ContrasenaHash'], $input['ROL_RolID'])) {
            $id = $input['ID'];
            $nombre = $input['Nombre'];
            $apellidos = $input['Apellidos'];
            $email = $input['Email'];
            $contrasenaHash = $input['ContrasenaHash'];
            $rol_rolid = $input['ROL_RolID'];
            $tfa_secret = isset($input['TFA_Secret']) ? $input['TFA_Secret'] : null;

            $resultado = $usuariosCRUD->editar($id, $nombre, $apellidos, $email, $contrasenaHash, $rol_rolid, $tfa_secret);
            echo json_encode($resultado);
        } else {
            error_log("Datos incompletos para editar el usuario.\n", 3, 'C:\UPV\Biometria\EcoBreezeRepo-Servidor\src\projecto\logs\app.log');
            echo json_encode(['error' => 'Datos incompletos para editar el usuario.']);
        }
        break;

    case 'DELETE':
        // Maneja la solicitud DELETE
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        if ($id) {
            $resultado = $usuariosCRUD->borrar($id);
            echo json_encode($resultado);
        } else {
            error_log("ID del usuario no especificado.\n", 3, 'C:\UPV\Biometria\EcoBreezeRepo-Servidor\src\projecto\logs\app.log');
            echo json_encode(['error' => 'ID del usuario no especificado.']);
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
