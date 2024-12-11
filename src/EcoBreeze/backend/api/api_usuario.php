<?php 
require_once '../log.php';
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
        $token = $requestData['token'] ?? null;

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
            $resultado = $usuariosAccionesCRUD->registrar($nombre, $apellidos, $email, $contrasenaHash, $token);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito.']);
            } else {
                registrarError("Error al registrar el usuario: " . json_encode($requestData));
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
                        'Apellidos' => $usuario['data']['Apellidos'],
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
                    registrarError("Error al actualizar el token de huella: " . json_encode($resultado));
                    echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al actualizar el token de huella.']);
                }
            } catch (Exception $e) {
                // Captura cualquier error inesperado
                registrarError("Excepción al insertar token de huella: " . $e->getMessage());
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

        registrarError(json_encode($requestData));
    
        // Verifica si usuarioID y mac son proporcionados
        if ($usuarioID && $mac) {
            $resultado = $usuariosAccionesCRUD->insertarSensor($usuarioID, $mac);
    
            // Maneja el resultado de la inserción
            if (isset($resultado['success'])) {
                echo json_encode(['success' => true, 'message' => $resultado['success']]);
            } else {
                registrarError("Error al insertar sensor: " . json_encode($resultado));
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
    
        registrarError(json_encode($requestData));
    
        // Verifica si se proporciona el ID del usuario, la contraseña actual y la nueva contraseña
        if ($id && $contrasenaActual && $nuevaContrasena) {
            $resultado = $usuariosAccionesCRUD->cambiarContrasenaPorID($id, $contrasenaActual, $nuevaContrasena);
    
            // Maneja el resultado de la actualización de la contraseña
            if (isset($resultado['success']) && $resultado['success']) {
                echo json_encode(['success' => true, 'message' => $resultado['message']]);
            } else {
                registrarError("Error al cambiar contraseña: " . json_encode($resultado));
                echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al cambiar la contraseña.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'La contraseña actual y la nueva contraseña son obligatorios.']);
        }
    break;

    case 'cambiar_token':
        $id = $requestData['id'] ?? null; // Obtener el ID del usuario del request
        $contrasenaActual = $requestData['contrasena_actual'] ?? null; // Obtener la contraseña actual del request
        $nuevoCorreo = $requestData['nuevo_correo'] ?? null; // Obtener el nuevo correo del request
        $token = $requestData['token'] ?? null; // Obtener el token del request
        registrarError($id . $contrasenaActual . $nuevoCorreo . 'que tal');
        // Verifica si se proporciona el ID del usuario, la contraseña actual, el nuevo correo y el token
        if ($id && $contrasenaActual && $nuevoCorreo && $token) {
            // Verifica si el nuevo correo ya está en uso
            if ($usuariosConsultasCRUD->emailExistente($nuevoCorreo)) {
                echo json_encode(['success' => false, 'error' => 'El email ya está registrado.']);
                break;
            }
    
            $resultado = $usuariosAccionesCRUD->cambiarTokenPorID($id, $contrasenaActual, $token);
    
            // Maneja el resultado de la actualización del token
            if (isset($resultado['success']) && $resultado['success']) {
                echo json_encode(['success' => true, 'message' => 'Por favor, verifique su nuevo correo.']);
            } else {
                registrarError("Error al cambiar token: " . json_encode($resultado));
                echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al cambiar el token.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'La contraseña actual, el nuevo correo y el token son obligatorios.']);
        }
        break;
    

    case 'recuperar_contrasena':
        $email = $requestData['email'] ?? null; // Obtener el correo electrónico del request
        $token = $requestData['token'] ?? null; // Obtener el token de recuperación del request
        $nuevaContrasena = $requestData['nueva_contrasena'] ?? null; // Obtener la nueva contraseña del request
        
        // Verifica si se proporciona el email, el token y la nueva contraseña
        if ($email && $token && $nuevaContrasena) {
            $resultado = $usuariosAccionesCRUD->recuperarContrasena($email, $token, $nuevaContrasena);
    
            // Maneja el resultado de la recuperación de la contraseña
            if (isset($resultado['success']) && $resultado['success']) {
                echo json_encode(['success' => true, 'message' => $resultado['success']]);
            } else {
                registrarError("Error al recuperar contraseña: " . json_encode($resultado));
                echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al recuperar la contraseña.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'El correo electrónico, token y la nueva contraseña son obligatorios.']);
        }
    break;

    case 'cambiar_contrasena_recuperar':
        $email = $requestData['email'] ?? null; // Obtener el correo del usuario del request
        $nuevaContrasena = $requestData['nueva_contrasena'] ?? null; // Obtener la nueva contraseña del request

        $mensaje = "Soy el api " .$email;
        registrarError($mensaje);

        // Verifica si se proporciona el correo y la nueva contraseña
        if ($email && $nuevaContrasena) {
            // Llamar al método para cambiar la contraseña por correo
            $resultado = $usuariosAccionesCRUD->cambiarContrasenaPorCorreo($email, $nuevaContrasena);

            // Maneja el resultado de la actualización de la contraseña
            if (isset($resultado['success']) && $resultado['success']) {
                echo json_encode(['success' => true, 'message' => $resultado['message']]);
            } else {
                registrarError("Error al cambiar contraseña: " . json_encode($resultado));
                echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al cambiar la contraseña.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'El correo electrónico y la nueva contraseña son obligatorios.']);
        }
        break;

    // FIN CASOS CAMBIO DATOS USUARIO

    // CASOS ADMIN
    case 'obtener_ultima_medicion':
    // Consultar la última medición de los usuarios con rol 2
    $result = $usuariosConsultasCRUD->obtenerUltimaMedicionDeUsuariosRol2();

    if ($result['success']) {
        // Si se encontraron usuarios con mediciones, se devuelve la lista
        echo json_encode(['success' => true, 'usuarios' => $result['usuarios']]);
    } else {
        // Si no se encontraron usuarios o no tienen mediciones
        echo json_encode(['success' => false, 'error' => $result['error']]);
    }
    break;

    // FIN CASOS ADMIN

    // CASOS UNIVERSALES
    case 'actualizar_token_recuperacion':
        $email = $requestData['email'] ?? null;
        $token = $requestData['token'] ?? null;
    
        registrarError("Datos recibidos: " . json_encode($requestData));
    
        if ($email && $token) {
            // Llamamos al método para actualizar el token de recuperación
            $resultado = $usuariosAccionesCRUD->actualizarTokenRecuperacion($email, $token);
        
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
                registrarError("Error al obtener datos del usuario: " . json_encode($resultado));
                echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al obtener los datos del usuario.']);
            }
        } else {
            // Si no se proporciona el ID, devuelve un error
            echo json_encode(['success' => false, 'error' => 'El ID del usuario es obligatorio.']);
        }
    break;

    case 'verificar_token':
        // Obtener datos del request
        $email = $requestData['email'] ?? null;
        $token = $requestData['token'] ?? null;
    
        if ($email && $token) {
            // Llamar a la función verificarTokenValido del CRUD
            $resultado = $usuariosConsultasCRUD->verificarTokenValido($email, $token);
    
            // Devolver la respuesta basada en el resultado del CRUD
            if ($resultado['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => $resultado['message'],
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => $resultado['error']
                ]);
            }
        } else {
            // Respuesta si faltan parámetros
            echo json_encode([
                'success' => false,
                'error' => 'URL no válida. Asegúrese de incluir email y token.'
            ]);
        }
        break;

    case 'cambiar_correo':
        $email = $requestData['email'] ?? null;
        $nuevoCorreo = $requestData['nuevo_correo'] ?? null;
                
        if ($email && $nuevoCorreo) {
            // Llamamos al método para cambiar el correo
            $resultado = $usuariosAccionesCRUD->cambiarCorreo($email, $nuevoCorreo);
            
            if (isset($resultado['error'])) {
                echo json_encode(['success' => false, 'message' => $resultado['error']]);
            } elseif (isset($resultado['success'])) {
                echo json_encode(['success' => true, 'message' => $resultado['success']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error inesperado.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Correo actual y nuevo correo son obligatorios.']);
        }
        break;
    

    case 'eliminar_usuario':
        $id = $requestData['id'] ?? null; // Obtener el ID del usuario del request
    
        // Verifica si se proporciona el ID del usuario
        if ($id) {
            // Llamamos al método de eliminación de usuario
            $resultado = $usuariosAccionesCRUD->eliminarUsuario($id);
    
            // Maneja el resultado de la eliminación
            if (isset($resultado['success']) && $resultado['success']) {
                echo json_encode(['success' => true, 'message' => $resultado['message']]);
            } else {
                registrarError("Error al eliminar usuario: " . json_encode($resultado));
                echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'Error desconocido al eliminar el usuario.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'El ID del usuario es obligatorio.']);
        }
        break;
        
    
    
    // FIN CASOS UNIVERSALES

    default:
    registrarError("Error: Acción no válida: $action");
        echo json_encode(['success' => false, 'error' => 'Acción no válida.']);
        break;
}
?>
