<?php 
require_once(__DIR__ . '/../../db/conexion.php');


/**
 * @class LogicaConsultaDatos
 * @brief Clase encargada de gestionar las consultas de datos en la base de datos.
 *
 * Esta clase se encarga de realizar operaciones de lectura en la base de datos, obteniendo información sobre
 * los usuarios, mediciones y otros registros sin modificar los datos existentes. Se enfoca en consultar y devolver
 * los datos solicitados de manera eficiente.
 *
 *   Métodos principales:
 *       1. Consultar la información de los usuarios o mediciones basándose en identificadores o filtros específicos.
 *       2. Obtener datos de la base de datos sin realizar modificaciones.
 *       3. Extraer la información relevante para ser utilizada en otros procesos o lógica de negocio.
 *
 * @note Utiliza `PDO` para realizar consultas seguras y eficientes a la base de datos.
 * @note Esta clase solo obtiene datos sin alterar los registros existentes en la base de datos.
 * @note Requiere un objeto de conexión a la base de datos previamente configurado.
 */

class UsuariosConsultasCRUD {
    private $conn;
    // Modificación del constructor para aceptar la conexión existente y definir el archivo de log
    public function __construct($conn) {
        $this->conn = $conn; // Usar la conexión pasada como argumento
        date_default_timezone_set('Europe/Madrid'); // Establecer la zona horaria
    }

    /* ------------------------------------------------------------------------------------------
     * 
     * METODOS CONSULTAS LOGIN
     * 
     *///----------------------------------------------------------------------------------------

    /**
     * Realiza el proceso de login verificando las credenciales del usuario.
     *
     * Este método permite que un usuario se loguee proporcionando su email y contraseña. 
     * Se verifica que el email esté registrado, que el usuario esté verificado, y que la contraseña sea correcta.
     * Si el usuario no está verificado, se comprobará si su token ha expirado. Si el token ha expirado, 
     * se eliminará al usuario de la base de datos. En caso de éxito, se devolverán los datos del usuario.
     *
     * Diseño:
     *
     * Entrada:
     *   - email (string, el correo electrónico del usuario).
     *   - contrasena (string, la contraseña ingresada por el usuario).
     *
     * Proceso:
     *   1. Verificar si el email está registrado en la base de datos.
     *   2. Si el usuario no está verificado, comprobar si el token de verificación ha expirado.
     *   3. Si el token ha expirado, eliminar al usuario de la base de datos.
     *   4. Si el usuario está verificado, verificar la contraseña utilizando `password_verify`.
     *   5. Si las credenciales son correctas, devolver los datos del usuario (ID, Nombre, Apellidos, Rol).
     *   6. Si las credenciales son incorrectas, devolver un mensaje de error.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": true,
     *       "data": {
     *         "ID": "usuario_id",
     *         "Nombre": "usuario_nombre",
     *         "Apellidos": "usuario_apellidos",
     *         "Rol": "usuario_rol"
     *       }
     *     }
     *   - En caso de error:
     *     {
     *       "success": false,
     *       "error": "Mensaje de error específico"
     *     }
     *
     * @param string $email El correo electrónico del usuario.
     * @param string $contrasena La contraseña ingresada por el usuario.
     * @return array Un array con el resultado de la autenticación.
     */

    public function login($email, $contrasena) {
        try {
            // Verificar si el email está registrado
            $stmt = $this->conn->prepare("SELECT ID, Nombre, Apellidos, ROL_RolID, ContrasenaHash, Verificado, expiracion_token FROM USUARIO WHERE Email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
    
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Si el usuario no existe, devolver error específico
            if (!$usuario) {
                return ['success' => false, 'error' => 'Correo no registrado'];
            }
    
            // Comprobar si el usuario está verificado
            if ($usuario['Verificado'] != 1) {
                // Si no está verificado, comprobamos si el token ha caducado
                $fechaActual = new DateTime();
                $expiracionToken = new DateTime($usuario['expiracion_token']);

                // Comparar si la hora actual es posterior a la de expiracion_token
                if ($fechaActual > $expiracionToken) {
                    // Eliminar el usuario si el token ha expirado
                    $deleteQuery = "DELETE FROM USUARIO WHERE ID = ?";
                    $deleteStmt = $this->conn->prepare($deleteQuery);
                    $deleteStmt->execute([$usuario['ID']]);

                    return ['success' => false, 'error' => 'No se ha verificado el correo y el token ha expirado. El usuario ha sido eliminado.'];
                } else {
                    return ['success' => false, 'error' => 'El correo no ha sido verificado. Por favor, revisa tu correo.'];
                }
            }
            // Si el usuario existe y está verificado, verificar la contraseña
            if (password_verify($contrasena, $usuario['ContrasenaHash'])) {
                return [
                    'success' => true,
                    'data' => [
                        'ID' => $usuario['ID'],
                        'Nombre' => $usuario['Nombre'],
                        'Apellidos' => $usuario['Apellidos'],
                        'Rol' => $usuario['ROL_RolID']
                    ]
                ]; // Devuelve los datos del usuario si las credenciales son correctas
            } else {
                return ['success' => false, 'error' => 'Contraseña incorrecta'];
            }
        } catch (PDOException $e) {
            registrarError("Error al verificar credenciales: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al verificar las credenciales'];
        }
    }

    /**
     * Realiza el login mediante el token de huella y el correo electrónico del usuario.
     *
     * Este método permite que un usuario inicie sesión utilizando su correo electrónico y el token de huella 
     * previamente registrado en la base de datos. Se verifica que el correo electrónico y el token de huella coincidan 
     * en la base de datos. Si las credenciales son correctas, se devuelven los datos del usuario; de lo contrario, 
     * se devuelve un mensaje de error.
     *
     * Diseño:
     *
     * Entrada:
     *   - email (string, el correo electrónico del usuario).
     *   - token_huella (string, el token de huella proporcionado por el usuario).
     *
     * Proceso:
     *   1. Verificar si el correo electrónico y el token de huella coinciden en la base de datos.
     *   2. Si no se encuentra una coincidencia, devolver un mensaje de error indicando que el token o el correo es inválido.
     *   3. Si la coincidencia es exitosa, devolver los datos del usuario (ID, Nombre, Rol).
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": true,
     *       "data": {
     *         "ID": "usuario_id",
     *         "Nombre": "usuario_nombre",
     *         "Rol": "usuario_rol"
     *       }
     *     }
     *   - En caso de error:
     *     {
     *       "success": false,
     *       "error": "Token de huella o correo electrónico inválido"
     *     }
     *
     * @param string $email El correo electrónico del usuario.
     * @param string $token_huella El token de huella proporcionado por el usuario.
     * @return array Un array con el resultado de la autenticación.
     */

    public function loginHuella($email, $token_huella) {
        try {
            // Prepara la consulta para verificar el email y el token de huella
            $stmt = $this->conn->prepare("SELECT ID, Nombre, Apellidos, ROL_RolID FROM USUARIO WHERE Email = :email AND token_huella = :token_huella");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':token_huella', $token_huella);
            $stmt->execute();
    
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Si no hay coincidencia para email y token de huella
            if (!$usuario) {
                return ['success' => false, 'error' => 'Token de huella o correo electrónico inválido'];
            }
    
            // Si se verifica el token y el email, devuelve los datos del usuario
            return [
                'success' => true,
                'data' => [
                    'ID' => $usuario['ID'],
                    'Nombre' => $usuario['Nombre'],
                    'Rol' => $usuario['ROL_RolID']
                ]
            ];
        } catch (PDOException $e) {
            registrarError("Error al verificar email y token de huella: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al verificar email y token de huella'];
        }
    }

    /**
     * Obtiene el token de huella asociado a un correo electrónico.
     *
     * Este método permite obtener el token de huella de un usuario a partir de su correo electrónico. Si el usuario 
     * existe y tiene un token de huella registrado, se devuelve dicho token. Si no se encuentra el usuario o no 
     * tiene un token de huella asociado, se devuelve `null`.
     *
     * Diseño:
     *
     * Entrada:
     *   - email (string, el correo electrónico del usuario).
     *
     * Proceso:
     *   1. Verificar si existe un registro de usuario con el correo electrónico proporcionado.
     *   2. Si se encuentra el usuario, devolver el token de huella asociado.
     *   3. Si no se encuentra el usuario, devolver `null`.
     *
     * Salida:
     *   - En caso de éxito, el token de huella del usuario.
     *   - En caso de error o si no se encuentra el usuario, `null`.
     *
     * @param string $email El correo electrónico del usuario.
     * @return string|null El token de huella del usuario o `null` si no se encuentra el usuario o no tiene un token de huella.
     */

    // Ayuda a hacer el login con la huella.
    public function obtenerHuella($email) {
        try {
            // Consultar el token de huella asociado al correo electrónico
            $stmt = $this->conn->prepare("SELECT token_huella FROM USUARIO WHERE Email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
        
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($usuario) {
                return $usuario['token_huella']; // Retornar el token de huella
            } else {
                return null; // Si no se encuentra el usuario
            }
        } catch (PDOException $e) {
            registrarError("Error al obtener el token de huella: " . $e->getMessage(). "\n");
            return null;
        }
    }
    /* ------------------------------------------------------------------------------------------
     *
     * METODOS CONSULTAS PARA ADMIN
     * 
     *///----------------------------------------------------------------------------------------
    // Método para obtener la última medición de todos los usuarios con rol 2

    /**
     * Obtiene la última medición de todos los usuarios con rol 2.
     *
     * Este método consulta la base de datos para obtener la última medición registrada para cada usuario con rol 2. 
     * La medición se obtiene a través de la relación entre las tablas `USUARIO`, `SENSOR` y `MEDICION`. Si un usuario 
     * no tiene ninguna medición, se asigna 'N/A' como valor de su última medición.
     *
     * Diseño:
     *
     * Entrada: Ninguna.
     * Proceso:
     *   1. Realiza una consulta SQL que obtiene la última medición de cada usuario con rol 2.
     *   2. Si un usuario no tiene mediciones, su valor de última medición será 'N/A'.
     *   3. Se devuelve una lista de usuarios con su última medición, ordenada de más reciente a más antigua.
     *
     * Salida:
     *   - En caso de éxito, devuelve una lista de usuarios con sus respectivas últimas mediciones.
     *   - En caso de error, devuelve un mensaje de error detallado.
     *
     * @return array El resultado de la consulta, con el estado de la operación y los datos obtenidos.
     */

    public function obtenerUltimaMedicionDeUsuariosRol2() {
        try {
            // Preparamos la consulta para obtener la última medición de todos los usuarios con rol 2
            $query = "SELECT 
                        u.ID, 
                        u.Nombre, 
                        u.Apellidos, 
                        u.Email, 
                        IFNULL(MAX(m.Fecha), 'N/A') AS UltimaMedicion
                    FROM 
                        USUARIO u
                    LEFT JOIN 
                        SENSOR s ON u.ID = s.USUARIO_ID  -- Relacionamos USUARIO con SENSOR
                    LEFT JOIN 
                        MEDICION m ON s.SensorID = m.SENSOR_ID_Sensor  -- Relacionamos SENSOR con MEDICION
                    WHERE 
                        u.ROL_RolID = 2
                    GROUP BY 
                        u.ID, u.Nombre, u.Apellidos, u.Email
                    ORDER BY 
                        UltimaMedicion DESC"; // Ordenamos de las más recientes a las más antiguas

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            // Verificamos si se encontraron resultados
            if ($stmt->rowCount() > 0) {
                // Obtenemos todos los usuarios y su última medición
                $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Aseguramos que aquellos usuarios sin mediciones tengan 'N/A'
                foreach ($usuarios as &$usuario) {
                    if ($usuario['UltimaMedicion'] === 'N/A') {
                        $usuario['UltimaMedicion'] = 'N/A';  // Rellenamos explícitamente con 'N/A'
                    }
                }

                return ['success' => true, 'usuarios' => $usuarios];
            } else {
                // No se encontraron usuarios con mediciones
                return ['success' => true, 'usuarios' => []];  // Devuelve un array vacío en lugar de un error
            }
        } catch (PDOException $e) {
            // Registro de error detallado para depuración
            registrarError("Error en obtener última medición de los usuarios: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al obtener la última medición de los usuarios.'];
        }
    }



    /* ------------------------------------------------------------------------------------------
     *
     * METODOS CONSULTAS UNIVERSALES
     * 
     *///----------------------------------------------------------------------------------------
    // Método para verificar si el email ya está registrado

    /**
     * Verifica si el email ya está registrado en la base de datos.
     *
     * Este método consulta la base de datos para verificar si un correo electrónico ya existe en la tabla `USUARIO`. 
     * Retorna un valor booleano indicando si el email está registrado o no.
     *
     * Diseño:
     *
     * Entrada:
     *   - `$email`: El correo electrónico a verificar en la base de datos.
     * Proceso:
     *   1. Realiza una consulta SQL que cuenta las filas donde el correo electrónico coincide con el proporcionado.
     *   2. Si el resultado es mayor que 0, significa que el email ya está registrado.
     *
     * Salida:
     *   - `true`: Si el correo electrónico ya está registrado.
     *   - `false`: Si no está registrado o si ocurre un error en la consulta.
     *
     * @param string $email El correo electrónico que se desea verificar.
     * @return bool `true` si el correo electrónico está registrado, `false` si no lo está o si hay un error.
     */

    public function emailExistente($email) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(1) FROM USUARIO WHERE Email = ?");
            $stmt->execute([$email]);
            $count = $stmt->fetchColumn();
            return $count > 0; // Devuelve true si el email ya está registrado
        } catch (PDOException $e) {
            registrarError("Error al verificar si el email existe: " . $e->getMessage() . "\n");
            return false; // Manejar errores durante la verificación
        }
    }

    /**
     * Obtiene los datos de un usuario por su ID.
     *
     * Este método consulta la base de datos para obtener los detalles del usuario, como su ID, nombre, apellidos y correo electrónico, 
     * utilizando el ID proporcionado. Retorna un arreglo con los datos del usuario si existe, o un mensaje de error si no se encuentra.
     *
     * Diseño:
     *
     * Entrada:
     *   - `$id`: El ID del usuario cuyo datos se desean obtener.
     * Proceso:
     *   1. Realiza una consulta SQL que busca el usuario por su ID.
     *   2. Si el usuario existe, se obtiene su información.
     *   3. Si no se encuentra el usuario, se devuelve un mensaje de error.
     *
     * Salida:
     *   - `success`: `true` si los datos del usuario fueron obtenidos correctamente.
     *   - `usuario`: Los datos del usuario si es encontrado.
     *   - `success`: `false` si el usuario no es encontrado o si ocurre un error.
     *   - `error`: Mensaje de error si no se encuentra el usuario o si ocurre un problema durante la consulta.
     *
     * @param int $id El ID del usuario que se desea consultar.
     * @return array Un arreglo que contiene el estado de la operación y los datos del usuario o el error correspondiente.
     */

    // Método para obtener los datos de un usuario por ID
    public function obtenerDatosUsuarioPorID($id) {
        try {
            // Preparamos la consulta para obtener los datos del usuario por ID
            $query = "SELECT ID, Nombre, Apellidos, Email FROM USUARIO WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);

            // Verificamos si se encontró el usuario
            if ($stmt->rowCount() > 0) {
                // Obtenemos los datos del usuario
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                return ['success' => true, 'usuario' => $usuario];
            } else {
                return ['success' => false, 'error' => 'No se encontró el usuario.'];
            }
        } catch (PDOException $e) {
            registrarError("Error en obtener datos usuario: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al obtener los datos del usuario.'];
        }
    }

    /**
     * Verifica si el token de un usuario es válido y no ha expirado.
     *
     * Este método realiza una serie de verificaciones para asegurar que el token proporcionado para un usuario es válido, no ha caducado,
     * y corresponde al token almacenado en la base de datos. Si alguna de las verificaciones falla, se devuelve un mensaje de error adecuado.
     * Si todas las verificaciones son satisfactorias, se devuelve un mensaje de éxito.
     *
     * Diseño:
     *
     * Entrada:
     *   - `$email`: El correo electrónico del usuario cuyo token se desea verificar.
     *   - `$token`: El token proporcionado que se desea validar.
     * Proceso:
     *   1. Realiza una consulta SQL para obtener el token y su fecha de expiración asociada al correo proporcionado.
     *   2. Verifica si el token está presente, si coincide con el proporcionado y si no ha expirado.
     *   3. Si el token es válido, retorna éxito. Si alguna verificación falla, retorna el error correspondiente.
     *
     * Salida:
     *   - `success`: `true` si el token es válido y no ha expirado.
     *   - `message`: Mensaje que indica que el token es válido si la verificación es exitosa.
     *   - `success`: `false` si el token es inválido, no coincide, o ha expirado.
     *   - `error`: Mensaje de error detallado si alguna verificación falla.
     *
     * @param string $email El correo electrónico del usuario cuyo token se desea verificar.
     * @param string $token El token proporcionado por el usuario para verificar su validez.
     * @return array Un arreglo con el estado de la operación, ya sea éxito o error.
     */

    public function verificarTokenValido($email, $token) {
        try {
            // Consulta SQL para verificar el token y la fecha de expiración
            $query = "SELECT ID, token, expiracion_token 
                      FROM USUARIO 
                      WHERE Email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Verificar si se encontró un usuario
            if (!$usuario) {
                return [
                    'success' => false,
                    'error' => 'No se encontró un usuario con el correo proporcionado.'
                ];
            }
    
            // Verificar si el token es válido
            if (empty($usuario['token']) || $usuario['token'] === '0') {
                return [
                    'success' => false,
                    'error' => 'El token no es válido. Por favor, solicite uno nuevo.'
                ];
            }
    
            // Verificar si el token coincide
            if ($usuario['token'] !== $token) {
                return [
                    'success' => false,
                    'error' => 'El token proporcionado no coincide.'
                ];
            }
    
            // Verificar si el token ha expirado
            $fechaActual = new DateTime();
            $fechaExpiracion = new DateTime($usuario['expiracion_token']);
    
            if ($fechaActual > $fechaExpiracion) {
                return [
                    'success' => false,
                    'error' => 'El token ha expirado. Por favor, solicite uno nuevo.'
                ];
            }
    
            // Retornar éxito con el ID del usuario si todas las verificaciones pasan
            return [
                'success' => true,
                'message' => 'El token es válido y no ha expirado.',
            ];
        } catch (PDOException $e) {
            // Registrar el error en el log
            registrarError("Error al verificar el token para el email $email: " . $e->getMessage());
    
            // Retornar un mensaje de error genérico
            return [
                'success' => false,
                'error' => 'Hubo un error al verificar el token.'
            ];
        }
    }    
    
}
?>
