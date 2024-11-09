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

// Obtener el valor de 'action' desde la URL usando $_GET
$action = $_GET['action'] ?? null;

// Decodificar el cuerpo JSON para obtener otros parámetros
$requestData = json_decode(file_get_contents('php://input'), true);

// Manejar las acciones según el valor de 'action'
switch ($action) {
    case 'registrar':
        // Validar y procesar la acción de registro
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
                logMessage("Error al registrar el usuario: " . json_encode($requestData));
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
            $usuario = $usuariosCRUD->verificarCredencialesCompleto($email, $contrasena);
    
            if (isset($usuario['error'])) {
                echo json_encode(['success' => false, 'error' => $usuario['error']]);
            } elseif ($usuario['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso.',
                    'usuario' => [
                        'ID' => $usuario['data']['ID'],
                        'Nombre' => $usuario['data']['Nombre'],
                        'Rol' => $usuario['data']['Rol']
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error inesperado.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Email y contraseña son obligatorios.']);
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

        case 'insertar_sensor':
            $usuarioID = $requestData['usuario_id'] ?? null; // Obtener usuario ID del request
            $mac = $requestData['mac'] ?? null; // Obtener MAC del request

            logMessage(json_encode($requestData));
        
            // Verifica si usuarioID y mac son proporcionados
            if ($usuarioID && $mac) {
                $resultado = $usuariosCRUD->insertarSensor($usuarioID, $mac);
        
                // Maneja el resultado de la inserción
                if (isset($resultado['success'])) {
                    echo json_encode(['success' => true, 'message' => $resultado['success']]);
                } else {
                    logMessage("Error al insertar sensor: " . json_encode($resultado));
                    echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al insertar el sensor.']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Usuario ID y MAC son obligatorios.']);
            }
            break;

                case 'obtener_datos_usuario':
                    $id = $requestData['id'] ?? null; // Obtener el ID del usuario del request
            
                    // Registra en el log
                    logMessage(json_encode($requestData));
            
                    // Verifica si se proporciona el ID del usuario
                    if ($id) {
                        // Llama al CRUD para obtener los datos del usuario
                        $resultado = $usuariosCRUD->obtenerDatosUsuarioPorID($id);
            
                        // Maneja el resultado de la obtención de datos
                        if (isset($resultado['success']) && $resultado['success']) {
                            // Si la consulta fue exitosa, devuelve los datos del usuario
                            echo json_encode(['success' => true, 'usuario' => $resultado['usuario']]);
                        } else {
                            // Si hay algún error, lo registra y devuelve un mensaje de error
                            logMessage("Error al obtener datos del usuario: " . json_encode($resultado));
                            echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al obtener los datos del usuario.']);
                        }
                    } else {
                        // Si no se proporciona el ID, devuelve un error
                        echo json_encode(['success' => false, 'error' => 'El ID del usuario es obligatorio.']);
                    }
                    break;
            

                case 'cambiar_contrasena':
                    $id = $requestData['id'] ?? null; // Obtener el ID del usuario del request
                    $contrasenaActual = $requestData['contrasena_actual'] ?? null; // Obtener la contraseña actual del request
                    $nuevaContrasena = $requestData['nueva_contrasena'] ?? null; // Obtener la nueva contraseña del request
                
                    logMessage(json_encode($requestData));
                
                    // Verifica si se proporciona el ID del usuario, la contraseña actual y la nueva contraseña
                    if ($id && $contrasenaActual && $nuevaContrasena) {
                        $resultado = $usuariosCRUD->cambiarContrasenaPorID($id, $contrasenaActual, $nuevaContrasena);
                
                        // Maneja el resultado de la actualización de la contraseña
                        if (isset($resultado['success']) && $resultado['success']) {
                            echo json_encode(['success' => true, 'message' => $resultado['message']]);
                        } else {
                            logMessage("Error al cambiar contraseña: " . json_encode($resultado));
                            echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al cambiar la contraseña.']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'error' => 'La contraseña actual y la nueva contraseña son obligatorios.']);
                    }
                    break;

                    case 'cambiar_correo':
                        $id = $requestData['id'] ?? null; // Obtener el ID del usuario del request
                        $contrasenaActual = $requestData['contrasena_actual'] ?? null; // Obtener la contraseña actual del request
                        $nuevoCorreo = $requestData['nuevo_correo'] ?? null; // Obtener el nuevo correo del request
                    
                        logMessage(json_encode($requestData)); // Revisa el contenido de $requestData
                    
                        // Verifica si se proporciona el ID del usuario, la contraseña actual y el nuevo correo
                        if ($id && $contrasenaActual && $nuevoCorreo) {
                            // Verifica si el nuevo correo ya está en uso
                            if ($usuariosCRUD->emailExistente($nuevoCorreo)) {
                                echo json_encode(['success' => false, 'error' => 'El email ya está registrado.']);
                                break;
                            }
                    
                            $resultado = $usuariosCRUD->cambiarCorreoPorID($id, $contrasenaActual, $nuevoCorreo);
                    
                            // Maneja el resultado de la actualización del correo
                            if (isset($resultado['success']) && $resultado['success']) {
                                echo json_encode(['success' => true, 'message' => $resultado['message']]);
                            } else {
                                logMessage("Error al cambiar correo: " . json_encode($resultado));
                                echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al cambiar el correo.']);
                            }
                        } else {
                            echo json_encode(['success' => false, 'error' => 'La contraseña actual y el nuevo correo son obligatorios.']);
                        }
                        break;
                    
                    
                    
    default:
        logMessage("Error: Acción no válida: $action");
        echo json_encode(['success' => false, 'error' => 'Acción no válida.']);
        break;
}
?>
