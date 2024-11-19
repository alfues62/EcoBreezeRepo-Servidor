<?php 
require_once(__DIR__ . '/../../db/conexion.php');
require_once(__DIR__ . '/../controllers/usuario_acciones_CRUD.php');
require_once(__DIR__ . '/../controllers/usuario_consultas_CRUD.php');

header('Content-Type: application/json');

// Configurar la zona horaria
date_default_timezone_set('Europe/Madrid');

// Crear una instancia de la clase de conexión
$conn = new Conexion();
$connection = $conn->getConnection();

// Crear una instancia de UsuariosCRUD
$usuariosAccionesCRUD = new UsuariosAccionesCRUD($connection);
$usuariosConsultasCRUD = new UsuariosConsultasCRUD($connection);

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
    // REGSITRO Y LOGIN
    case 'registrar':
        // Validar y procesar la acción de registro
        $nombre = $requestData['nombre'] ?? null;
        $apellidos = $requestData['apellidos'] ?? null;
        $email = $requestData['email'] ?? null;
        $contrasena = $requestData['contrasena'] ?? null;
        $token_verficicacion = $requestData['token_verficicacion'] ?? null;

        // Validar que todos los campos necesarios estén presentes
        if ($nombre && $apellidos && $email && $contrasena) {
            // Validar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'error' => 'Formato de email inválido.']);
                break;
            }

            // Verificar si el email ya está en uso
            if ($usuariosConsultasCRUD->emailExistente($email)) {
                echo json_encode(['success' => false, 'error' => 'El email ya está registrado.']);
                break;
            }

            // Hash de la contraseña con BCRYPT
            $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

            // Llamar al método de inserción
            $resultado = $usuariosAccionesCRUD->registrar($nombre, $apellidos, $email, $contrasenaHash, $token_verficicacion);

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

    case 'verificar_correo':
        $email = $requestData['email'] ?? null;
        $token = $requestData['token'] ?? null;
    
        if ($email && $token) {
            // Llamamos al método para verificar el correo con el token
            $resultado = $usuariosAccionesCRUD->verificarCorreo($email, $token);
    
            // Comprobamos si hay error en el proceso de verificación
            if (isset($resultado['error'])) {
                echo json_encode(['success' => false, 'error' => $resultado['error']]);
            } elseif (isset($resultado['success'])) {
                echo json_encode([
                    'success' => true,
                    'message' => $resultado['success']
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error inesperado.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Email y token son obligatorios.']);
        }
    break;

    case 'iniciar_sesion':
        $email = $requestData['email'] ?? null;
        $contrasena = $requestData['contrasena'] ?? null;
    
        if ($email && $contrasena) {
            $usuario = $usuariosConsultasCRUD->login($email, $contrasena);
    
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

    case 'iniciar_sesion_huella':
        $email = $requestData['email'] ?? null;
        $token_huella = $requestData['token_huella'] ?? null;
    
        // Verificar que el email y el token de huella estén presentes
        if ($email && $token_huella) {
            // Llamar a la función para verificar las credenciales con email y token de huella
            $usuario = $usuariosConsultasCRUD->loginHuella($email, $token_huella);
    
            // Verificar el resultado de la autenticación
            if (isset($usuario['error'])) {
                // Si hay un error, devolverlo en la respuesta
                echo json_encode(['success' => false, 'error' => $usuario['error']]);
            } elseif ($usuario['success']) {
                // Si la autenticación fue exitosa, devolver los datos del usuario
                echo json_encode([
                    'success' => true,
                    'message' => 'Inicio de sesión con huella y correo exitoso.',
                    'usuario' => [
                        'ID' => $usuario['data']['ID'],
                        'Nombre' => $usuario['data']['Nombre'],
                        'Rol' => $usuario['data']['Rol']
                    ]
                ]);
            } else {
                // En caso de un fallo inesperado
                echo json_encode(['success' => false, 'error' => 'Error inesperado.']);
            }
        } else {
            // Si faltan los datos requeridos, devolver un error
            echo json_encode(['success' => false, 'error' => 'El correo electrónico y el token de huella son obligatorios.']);
        }
    break;
    // FIN REGISTRO Y LOGIN

    // CASOS PARA HUELLAS
    case 'obtener_token_huella':
        $email = $_GET['email'] ?? null;  // Usar $_GET en vez de $requestData

        logMessage($email);

        if ($email) {
            // Consultar el token de huella asociado al email
            $tokenHuella = $usuariosConsultasCRUD->obtenerHuella($email);

            if ($tokenHuella) {
                echo json_encode(['success' => true, 'token_huella' => $tokenHuella]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se encontró un token de huella para este correo']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'El correo electrónico es obligatorio']);
        }
    break;


    case 'insertar_huella':
        // Obtener los parámetros del request
        $id = $requestData['id'] ?? null; // ID del usuario
        $tokenHuella = $requestData['token_huella'] ?? null; // Token de huella

        logMessage(json_encode($requestData)); // Log para revisar el contenido de la solicitud

        // Verifica si se proporcionan ambos parámetros
        if ($id && $tokenHuella) {

            // Intentar insertar el token de huella
            try {
                // Llamamos a la función para guardar el token de huella en la base de datos
                $resultado = $usuariosAccionesCRUD->subirTokenHuella($id, $tokenHuella);

                // Maneja el resultado de la operación
                if (isset($resultado['success']) && $resultado['success']) {
                    echo json_encode(['success' => true, 'message' => 'Token de huella guardado correctamente.']);
                } else {
                    logMessage("Error al actualizar el token de huella: " . json_encode($resultado));
                    echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al actualizar el token de huella.']);
                }
            } catch (Exception $e) {
                // Captura cualquier error inesperado
                logMessage("Excepción al insertar token de huella: " . $e->getMessage());
                echo json_encode(['success' => false, 'error' => 'Hubo un error al procesar la solicitud.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'El ID del usuario y el token de huella son obligatorios.']);
        }
    break;
    // FIN CASOS PARA HUELLAS

    // CASOS SENSOR
    case 'insertar_sensor':
        $usuarioID = $requestData['usuario_id'] ?? null; // Obtener usuario ID del request
        $mac = $requestData['mac'] ?? null; // Obtener MAC del request

        logMessage(json_encode($requestData));
    
        // Verifica si usuarioID y mac son proporcionados
        if ($usuarioID && $mac) {
            $resultado = $usuariosAccionesCRUD->insertarSensor($usuarioID, $mac);
    
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
    // FIN CASOS SENSOR

    // CASOS CAMBIO DATOS USUARIO
    case 'cambiar_contrasena':
        $id = $requestData['id'] ?? null; // Obtener el ID del usuario del request
        $contrasenaActual = $requestData['contrasena_actual'] ?? null; // Obtener la contraseña actual del request
        $nuevaContrasena = $requestData['nueva_contrasena'] ?? null; // Obtener la nueva contraseña del request
    
        logMessage(json_encode($requestData));
    
        // Verifica si se proporciona el ID del usuario, la contraseña actual y la nueva contraseña
        if ($id && $contrasenaActual && $nuevaContrasena) {
            $resultado = $usuariosAccionesCRUD->cambiarContrasenaPorID($id, $contrasenaActual, $nuevaContrasena);
    
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
            if ($usuariosConsultasCRUD->emailExistente($nuevoCorreo)) {
                echo json_encode(['success' => false, 'error' => 'El email ya está registrado.']);
                break;
            }
    
            $resultado = $usuariosAccionesCRUD->cambiarCorreoPorID($id, $contrasenaActual, $nuevoCorreo);
    
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

    case 'recuperar_contrasena':
        $email = $requestData['email'] ?? null; // Obtener el correo electrónico del request
        $token = $requestData['token'] ?? null; // Obtener el token de recuperación del request
        $nuevaContrasena = $requestData['nueva_contrasena'] ?? null; // Obtener la nueva contraseña del request
    
        logMessage(json_encode($requestData));
    
        // Verifica si se proporciona el email, el token y la nueva contraseña
        if ($email && $token && $nuevaContrasena) {
            $resultado = $usuariosAccionesCRUD->recuperarContrasena($email, $token, $nuevaContrasena);
    
            // Maneja el resultado de la recuperación de la contraseña
            if (isset($resultado['success']) && $resultado['success']) {
                echo json_encode(['success' => true, 'message' => $resultado['success']]);
            } else {
                logMessage("Error al recuperar contraseña: " . json_encode($resultado));
                echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al recuperar la contraseña.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'El correo electrónico, token y la nueva contraseña son obligatorios.']);
        }
    break;
    // FIN CASOS CAMBIO DATOS USUARIO

    // CASOS UNIVERSALES
    case 'actualizar_token_recuperacion':
        $email = $requestData['email'] ?? null;
        $token = $requestData['token'] ?? null;
    
        registrarError("Datos recibidos: " . json_encode($requestData));
    
        if ($email && $token) {
            // Llamamos al método para actualizar el token de recuperación
            $resultado = $usuariosAccionesCRUD->actualizarTokenRecuperacion($email, $token);
    
            registrarError("Resultado del método actualizarTokenRecuperacion: " . json_encode($resultado));
    
            if (isset($resultado['error'])) {
                echo json_encode(['success' => false, 'error' => $resultado['error']]);
            } elseif (isset($resultado['success'])) {
                echo json_encode([
                    'success' => true,
                    'message' => $resultado['success'],
                    'usuario' => [
                        'nombre' => $resultado['nombre'] ?? null,
                        'apellidos' => $resultado['apellidos'] ?? null,
                        'email' => $resultado['email'] ?? null
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error inesperado.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Email y nuevo token son obligatorios.']);
        }
    break;

    case 'obtener_datos_usuario':
        $id = $requestData['id'] ?? null; // Obtener el ID del usuario del request

        // Verifica si se proporciona el ID del usuario
        if ($id) {
            // Llama al CRUD para obtener los datos del usuario
            $resultado = $usuariosConsultasCRUD->obtenerDatosUsuarioPorID($id);

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
    // FIN CASOS UNIVERSALES

    default:
        logMessage("Error: Acción no válida: $action");
        echo json_encode(['success' => false, 'error' => 'Acción no válida.']);
        break;
}
?>
