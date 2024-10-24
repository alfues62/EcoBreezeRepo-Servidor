<?php
// Incluye el controlador de usuarios
require_once(__DIR__ . '/../../db/conexion.php');
require_once(__DIR__ . '/../controllers/usuario_CRUD.php');

$logFile = '/var/www/html/logs/app.log';

// Crea una instancia de la clase Conexion y obtiene la conexión
$conn = (new Conexion())->getConnection();

// Crea una instancia de la clase UsuariosCRUD
$usuariosCRUD = new UsuariosCRUD($conn); // Pasa la conexión al CRUD

// Obtiene el método HTTP de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
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
            // Registro de error en app.log
            error_log("[" . date('Y-m-d H:i:s') . "] Datos incompletos para insertar el usuario: " . json_encode($input) . "\n", 3, $logFile);
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
            // Registro de error en app.log
            error_log("[" . date('Y-m-d H:i:s') . "] Datos incompletos para editar el usuario: " . json_encode($input) . "\n", 3, $logFile);
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
            // Registro de error en app.log
            error_log("[" . date('Y-m-d H:i:s') . "] ID del usuario no especificado para eliminar.\n", 3, $logFile);
            echo json_encode(['error' => 'ID del usuario no especificado.']);
        }
        break;

    default:
        // Maneja métodos no permitidos
        http_response_code(405); // Método no permitido
        // Registro de error en app.log
        error_log("[" . date('Y-m-d H:i:s') . "] Método no permitido: $method\n", 3, $logFile);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>
