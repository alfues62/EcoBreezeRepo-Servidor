<?php 
require_once(__DIR__ . '/../../db/conexion.php');
require_once(__DIR__ . '/../controllers/usuario_CRUD.php');

header('Content-Type: application/json');

// Configurar la zona horaria
date_default_timezone_set('Europe/Madrid');

// Crear una instancia de la clase de conexión
$conn = new Conexion();
$connection = $conn->getConnection();

// Crear una instancia de UsuariosCRUD
$usuariosCRUD = new UsuariosCRUD($connection);

// Función para registrar logs
function logMessage($message) {
    $logFile = '/var/www/html/logs/app.log'; // Ruta del archivo log
    file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

// Decodificar la solicitud JSON
$requestData = json_decode(file_get_contents('php://input'), true);
$action = $requestData['action'] ?? null;

// Manejar las acciones según el valor de 'action'
switch ($action) {
    case 'registrar':
        $nombre = $requestData['nombre'] ?? null;
        $apellidos = $requestData['apellidos'] ?? null;
        $email = $requestData['email'] ?? null;
        $contrasena = $requestData['contrasena'] ?? null;
        $rol_rolid = $requestData['rol_rolid'] ?? 2; // Valor predeterminado para rol

        // Validar que todos los campos necesarios estén presentes
        if ($nombre && $apellidos && $email && $contrasena) {
            // Validar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'error' => 'Formato de email inválido.']);
                break;
            }

            // Verificar si el email ya está en uso
            if ($usuariosCRUD->emailExistente($email)) {
                echo json_encode(['success' => false, 'error' => 'El email ya está registrado.']);
                break;
            }

            // Hash de la contraseña con BCRYPT
            $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

            // Llamar al método de inserción
            $resultado = $usuariosCRUD->insertar($nombre, $apellidos, $email, $contrasenaHash, $rol_rolid);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al registrar el usuario.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios.']);
        }
        break;

    case 'iniciar_sesion':
        $email = $requestData['email'] ?? null;
        $contrasena = $requestData['contrasena'] ?? null;

        if ($email && $contrasena) {
            // Llamar a verificarCredencialesCompleto para verificar email y contraseña
            $usuario = $usuariosCRUD->verificarCredencialesCompleto($email, $contrasena);

            // Manejar la respuesta según el resultado de la verificación
            if (isset($usuario['error'])) {
                echo json_encode(['success' => false, 'error' => $usuario['error']]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso.',
                    'usuario_id' => $usuario['ID'], 
                    'usuario' => [
                        'ID' => $usuario['ID'],
                        'Nombre' => $usuario['Nombre'],
                        'Rol' => $usuario['Rol']
                    ]
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Email y contraseña son obligatorios.']);
        }
        break;

    case 'leer':
        $id = $requestData['id'] ?? null;
        $nombre = $requestData['nombre'] ?? null;
        $apellidos = $requestData['apellidos'] ?? null;
        $email = $requestData['email'] ?? null;

        $usuarios = $usuariosCRUD->leer($id, $nombre, $apellidos, $email);
        echo json_encode(['success' => true, 'data' => $usuarios]);
        break;

    case 'editar':
        $id = $requestData['id'] ?? null;
        $nombre = $requestData['nombre'] ?? null;
        $apellidos = $requestData['apellidos'] ?? null;
        $email = $requestData['email'] ?? null;
        $contrasena = $requestData['contrasena'] ?? null;
        $rol_rolid = $requestData['rol_rolid'] ?? null;
        $tfa_secret = $requestData['tfa_secret'] ?? null;

        if ($id && $nombre && $apellidos && $email && $contrasena && $rol_rolid) {
            $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);
            $resultado = $usuariosCRUD->editar($id, $nombre, $apellidos, $email, $contrasenaHash, $rol_rolid, $tfa_secret);
            echo json_encode(['success' => true, 'data' => $resultado]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios.']);
        }
        break;

    case 'borrar':
        $id = $requestData['id'] ?? null;

        if ($id) {
            $resultado = $usuariosCRUD->borrar($id);
            echo json_encode(['success' => true, 'data' => $resultado]);
        } else {
            echo json_encode(['success' => false, 'error' => 'ID de usuario es obligatorio.']);
        }
        break;

    default:
        logMessage("Error: Acción no válida: $action");
        echo json_encode(['success' => false, 'error' => 'Acción no válida.']);
        break;
}
?>
