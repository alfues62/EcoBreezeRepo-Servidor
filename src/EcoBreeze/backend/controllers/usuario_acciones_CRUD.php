<?php 
require_once(__DIR__ . '/../../db/conexion.php');

/**
 * @class LogicaFuncionesCRUD
 * @brief Clase encargada de gestionar las operaciones CRUD (Crear, Leer, Actualizar, Eliminar) en la base de datos.
 *
 * Esta clase se encarga de manejar todas las operaciones que interactúan directamente con la base de datos
 * de usuarios, mediciones y otros registros. Permite realizar la creación, lectura, actualización y eliminación
 * de datos en la base de datos, gestionando las consultas SQL necesarias para modificar los datos persistentes.
 *
 *   Métodos principales:
 *       1. Crear nuevos registros de usuarios, mediciones y otros elementos en la base de datos.
 *       2. Actualizar registros existentes con nueva información proporcionada.
 *       3. Eliminar registros de la base de datos de acuerdo a ciertos parámetros.
 *
 * @note Utiliza `PDO` para interactuar con la base de datos de manera segura y eficiente.
 * @note Esta clase realiza modificaciones en los datos de la base de datos, por lo que puede tener efectos permanentes.
 * @note Requiere un objeto de conexión a la base de datos previamente configurado.
 */


class UsuariosAccionesCRUD {
    private $conn;

    // Modificación del constructor para aceptar la conexión existente y definir el archivo de log
    public function __construct($conn) {
        $this->conn = $conn; // Usar la conexión pasada como argumento
        date_default_timezone_set('Europe/Madrid'); // Establecer la zona horaria
    }

    /* ------------------------------------------------------------------------------------------
     *
     * METODOS ACCION REGISTRO
     * 
     *///----------------------------------------------------------------------------------------
    // Método para insertar un nuevo usuario
    /**
     * Registra un nuevo usuario en la base de datos.
     *
     * Este método permite insertar un usuario con los datos proporcionados, estableciendo el rol predeterminado y configurando un token de verificación con una expiración de 24 horas.
     * Antes de la inserción, verifica si el correo electrónico ya está registrado. Si el registro existe pero el token ha expirado, se elimina el usuario y se procede con un nuevo registro.
     * En caso de éxito, se solicita al usuario verificar su correo.
     *
     * Diseño:
     * 
     * Entrada:
     *   - nombre (String)
     *   - apellidos (String)
     *   - email (String, único)
     *   - contrasenaHash (String, contraseña ya cifrada)
     *   - token (String, generado para la verificación)
     * 
     * Proceso:
     *   1. Verificar si el correo ya está registrado:
     *      - Si el token ha expirado, eliminar el registro previo.
     *      - Si no ha expirado:
     *          a. Si el usuario está verificado, devolver un mensaje de error.
     *          b. Si no está verificado, devolver un mensaje de registro pendiente.
     *   2. Insertar el usuario con los campos proporcionados y valores predeterminados.
     * 
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": "Usuario insertado con éxito. Por favor verifica tu correo."
     *     }
     *   - En caso de error:
     *     {
     *       "error": "Mensaje descriptivo del error."
     *     }
     * 
     * @param string $nombre El nombre del usuario.
     * @param string $apellidos Los apellidos del usuario.
     * @param string $email El correo electrónico del usuario.
     * @param string $contrasenaHash La contraseña cifrada del usuario.
     * @param string $token El token generado para la verificación.
     * @return array Un array con un mensaje de éxito o error.
     */

    public function registrar($nombre, $apellidos, $email, $contrasenaHash, $token) {
        try {
            // Establecer el rol como 2
            $rol_rolid = 2;

            // Obtener la hora actual
            $fechaActual = new DateTime();

            // Establecer la expiración del token a 24 horas a partir del momento actual para un nuevo usuario
            $fechaExpiracionToken = (new DateTime())->modify('+24 hours');

            // Verificar si ya existe un usuario con el mismo correo
            $queryCheck = "SELECT ID, expiracion_token, Verificado FROM USUARIO WHERE Email = ?";
            $stmtCheck = $this->conn->prepare($queryCheck);
            $stmtCheck->execute([$email]);
            $usuarioExistente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            // Si el usuario ya existe
            if ($usuarioExistente) {
                $expiracionToken = new DateTime($usuarioExistente['expiracion_token']);

                // Comparar si la hora actual es posterior a la de expiracion_token
                if ($fechaActual > $expiracionToken) {
                    // Eliminar el usuario si el token ha expirado
                    $deleteQuery = "DELETE FROM USUARIO WHERE ID = ?";
                    $deleteStmt = $this->conn->prepare($deleteQuery);
                    $deleteStmt->execute([$usuarioExistente['ID']]);
                } else {
                    // Si el token no ha expirado, verificar si ya está verificado
                    if ($usuarioExistente['Verificado'] == 1) {
                        return ['error' => 'Este correo ya está verificado y registrado.'];
                    } else {
                        return ['error' => 'Este correo ya está registrado y pendiente de verificación. Por favor, revisa tu correo.'];
                    }
                }
            }

            // Query para insertar el usuario con los nuevos campos
            $query = "INSERT INTO USUARIO (Nombre, Apellidos, Email, ContrasenaHash, Verificado, token, expiracion_token, ROL_RolID) 
                    VALUES (?, ?, ?, ?, 0, ?, ?, ?)";

            // Preparar y ejecutar la consulta
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nombre, $apellidos, $email, $contrasenaHash, $token, $fechaExpiracionToken->format('Y-m-d H:i:s'), $rol_rolid]);

            // Retornar éxito
            return ['success' => 'Usuario insertado con éxito. Por favor verifica tu correo.'];
        } catch (PDOException $e) {
            // Registrar el error
            registrarError("Error en insertar usuario: " . $e->getMessage() . "\n");
            return ['error' => 'Error al insertar un usuario'];
        }
    }

    /**
     * Verifica la dirección de correo electrónico de un usuario.
     *
     * Este método permite validar un correo electrónico utilizando un token de verificación. Si el token es válido y no ha expirado, el usuario es marcado como verificado.
     * Si el token ha expirado o es inválido, se devuelve un mensaje descriptivo y se elimina al usuario si corresponde.
     *
     * Diseño:
     * 
     * Entrada:
     *   - email (String, correo electrónico del usuario)
     *   - token (String, token de verificación)
     *
     * Proceso:
     *   1. Consultar al usuario en la base de datos utilizando el correo.
     *      - Si no existe, retornar error: "Correo no encontrado."
     *   2. Verificar si ya está marcado como verificado:
     *      - Si está verificado, retornar éxito: "El correo ya estaba verificado."
     *   3. Comparar la fecha de expiración del token con la fecha actual:
     *      - Si el token ha expirado, eliminar al usuario y retornar error: "El token ha expirado."
     *   4. Validar el token:
     *      - Si no coincide, retornar error: "Token no válido."
     *   5. Si las validaciones pasan, marcar al usuario como verificado:
     *      - Actualizar el estado a verificado y borrar el token.
     *      - Retornar éxito: "Correo verificado con éxito."
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": "Correo verificado con éxito."
     *     }
     *   - En caso de error:
     *     {
     *       "error": "Mensaje descriptivo del error."
     *     }
     *
     * @param string $email El correo electrónico a verificar.
     * @param string $token El token de verificación asociado al correo.
     * @return array Un array con un mensaje de éxito o error.
     */

    public function verificarCorreo($email, $token) {
        try {
            // Iniciar transacción
            $this->conn->beginTransaction();

            // Consultamos al usuario utilizando el correo y el token de verificación
            $query = "SELECT ID, token, Verificado, expiracion_token FROM USUARIO WHERE Email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificamos si el usuario existe
            if (!$usuario) {
                return ['error' => 'Correo no encontrado.'];
            }

            // Comprobamos si el usuario ya está verificado
            if ($usuario['Verificado'] == 1) {
                // Si el usuario ya está verificado, devolver mensaje de éxito
                return ['success' => 'El correo ya estaba verificado.'];
            }

            // Verificamos si el token ha expirado
            $fecha_actual = new DateTime();
            $expiracion_token = new DateTime($usuario['expiracion_token']);

            if ($fecha_actual > $expiracion_token) {
                // Eliminar al usuario si el token ha expirado
                $deleteQuery = "DELETE FROM USUARIO WHERE ID = ?";
                $deleteStmt = $this->conn->prepare($deleteQuery);
                $deleteStmt->execute([$usuario['ID']]);

                // Confirmar transacción y retornar mensaje de error
                $this->conn->commit();
                return ['error' => 'El token ha expirado. El usuario ha sido eliminado. Por favor, solicite uno nuevo.'];
            }

            // Comprobamos si el token es válido
            if ($usuario['token'] !== $token) {
                return ['error' => 'Token no válido.'];
            }

            // Si el token es válido y no ha expirado, verificamos al usuario
            $queryUpdate = "UPDATE USUARIO SET Verificado = 1, token = 0, expiracion_token = 0 WHERE Email = ?";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->execute([$email]);

            // Verificamos si la actualización se realizó correctamente
            if ($stmtUpdate->rowCount() > 0) {
                // Confirmar transacción
                $this->conn->commit();
                return ['success' => 'Correo verificado con éxito.'];
            } else {
                registrarError("No se pudo actualizar el estado del correo para email: $email");
                $this->conn->rollBack();
                return ['error' => 'No se pudo actualizar el estado del correo.'];
            }
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $this->conn->rollBack();
            // Registrar el error en el log
            registrarError("Error en verificar correo: " . $e->getMessage());
            return ['error' => 'Hubo un error al verificar el correo.'];
        }
    }
    /* ------------------------------------------------------------------------------------------
     *
     * METODOS ACCION MANEJO DE VALORES
     * 
     *///----------------------------------------------------------------------------------------
    
    /**
     * Inserta un nuevo sensor en la base de datos.
     *
     * Este método permite registrar un sensor asociado a un usuario en la base de datos utilizando su ID y la dirección MAC del sensor.
     * En caso de éxito, devuelve un mensaje confirmando la operación; si ocurre un error, registra el detalle en el log y devuelve un mensaje de error.
     *
     * Diseño:
     * 
     * Entrada:
     *   - usuarioID (int, ID del usuario al que se asociará el sensor)
     *   - mac (string, dirección MAC del sensor)
     *
     * Proceso:
     *   1. Preparar la consulta SQL para insertar el sensor.
     *   2. Ejecutar la consulta con los parámetros proporcionados.
     *   3. Retornar éxito si la inserción es exitosa.
     *   4. En caso de error, registrar el mensaje de error en el log y devolver un mensaje de error al cliente.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": "Sensor insertado con éxito."
     *     }
     *   - En caso de error:
     *     {
     *       "error": "Error al insertar el sensor."
     *     }
     *
     * @param int $usuarioID El ID del usuario al que se asociará el sensor.
     * @param string $mac La dirección MAC del sensor.
     * @return array Un array con un mensaje de éxito o error.
     */

    // Método para insertar un nuevo sensor
    public function insertarSensor($usuarioID, $mac) {
        try {
            $query = "INSERT INTO SENSOR (USUARIO_ID, MAC) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$usuarioID, $mac]);

            return ['success' => 'Sensor insertado con éxito.'];
        } catch (PDOException $e) {
            registrarError("Error en insertar sensor: " . $e->getMessage() . "/n");
            return ['error' => 'Error al insertar el sensor'];
        }
    }

    /**
     * Cambia la contraseña de un usuario por su ID.
     *
     * Este método permite actualizar la contraseña de un usuario en la base de datos.
     * Se verifica que la contraseña actual ingresada coincida con la almacenada antes de proceder con el cambio.
     * 
     * Diseño:
     * 
     * Entrada:
     *   - id (int, ID del usuario cuya contraseña se desea cambiar).
     *   - contrasenaActual (string, contraseña actual del usuario para verificar su autenticidad).
     *   - nuevaContrasena (string, nueva contraseña que será almacenada tras la verificación).
     *
     * Proceso:
     *   1. Obtener la contraseña almacenada del usuario por su ID.
     *   2. Verificar si el usuario existe.
     *   3. Comparar la contraseña actual ingresada con el hash almacenado.
     *   4. Si coincide:
     *      - Hashear la nueva contraseña.
     *      - Actualizar el hash de la contraseña en la base de datos.
     *      - Retornar éxito si se actualizó correctamente.
     *   5. Si no coincide, retornar un mensaje de error.
     *   6. Registrar en el log cualquier error ocurrido durante el proceso.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": true,
     *       "message": "Contraseña actualizada con éxito."
     *     }
     *   - En caso de error:
     *     {
     *       "success": false,
     *       "error": "Motivo del error."
     *     }
     *
     * @param int $id El ID del usuario cuya contraseña será actualizada.
     * @param string $contrasenaActual La contraseña actual del usuario para validar.
     * @param string $nuevaContrasena La nueva contraseña que será almacenada.
     * @return array Un array con un mensaje de éxito o error.
     */

    // Método para cambiar la contraseña de un usuario por ID
    public function cambiarContrasenaPorID($id, $contrasenaActual, $nuevaContrasena) {
        try {
            // Preparamos la consulta para obtener el hash de la contraseña actual
            $query = "SELECT ContrasenaHash FROM USUARIO WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);

            // Obtenemos el resultado
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificamos si se encontró el usuario
            if ($usuario) {
                // Comparamos la contraseña actual ingresada con el hash almacenado
                if (password_verify($contrasenaActual, $usuario['ContrasenaHash'])) {
                    // Hasheamos la nueva contraseña
                    $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

                    // Preparamos la consulta para actualizar la contraseña del usuario
                    $updateQuery = "UPDATE USUARIO SET ContrasenaHash = ? WHERE ID = ?";
                    $updateStmt = $this->conn->prepare($updateQuery);
                    $updateStmt->execute([$contrasenaHash, $id]);

                    // Verificamos si se actualizó la contraseña
                    if ($updateStmt->rowCount() > 0) {
                        return ['success' => true, 'message' => 'Contraseña actualizada con éxito.'];
                    } else {
                        return ['success' => false, 'error' => 'La contraseña ya es la misma o no se realizaron cambios.'];
                    }
                } else {
                    return ['success' => false, 'error' => 'La contraseña actual es incorrecta.'];
                }
            } else {
                return ['success' => false, 'error' => 'No se encontró el usuario.'];
            }
        } catch (PDOException $e) {
            registrarError("Error en cambiar contraseña: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al cambiar la contraseña.'];
        }
    }

    /**
     * Cambia el token de un usuario por su ID.
     *
     * Este método permite actualizar el token de un usuario en la base de datos tras validar su contraseña actual.
     * También establece una nueva fecha de expiración para el token, configurada a 24 horas a partir del momento de la actualización.
     *
     * Diseño:
     *
     * Entrada:
     *   - id (int, ID del usuario cuyo token se desea cambiar).
     *   - contrasenaActual (string, contraseña actual del usuario para validar su autenticidad).
     *   - token (string, nuevo token que será asignado al usuario).
     *
     * Proceso:
     *   1. Consultar la contraseña almacenada del usuario por su ID.
     *   2. Verificar si el usuario existe.
     *   3. Comparar la contraseña actual ingresada con el hash almacenado en la base de datos.
     *   4. Si coincide:
     *      - Preparar y ejecutar una consulta para actualizar el token y establecer su expiración a 24 horas desde el momento actual.
     *      - Verificar si el token fue actualizado correctamente.
     *      - Retornar éxito si la operación fue exitosa.
     *   5. Si no coincide, retornar un mensaje de error indicando que la contraseña es incorrecta.
     *   6. Registrar cualquier error ocurrido durante el proceso en un archivo de log.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": true,
     *       "message": "Token actualizado con éxito."
     *     }
     *   - En caso de error:
     *     {
     *       "success": false,
     *       "error": "Motivo del error."
     *     }
     *
     * @param int $id El ID del usuario cuyo token será actualizado.
     * @param string $contrasenaActual La contraseña actual del usuario para validar.
     * @param string $token El nuevo token que será asignado al usuario.
     * @return array Un array con un mensaje de éxito o error.
     */

    public function cambiarTokenPorID($id, $contrasenaActual, $token) {
        try {
            // Preparamos la consulta para obtener el hash de la contraseña actual
            $query = "SELECT ContrasenaHash FROM USUARIO WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);

            // Obtenemos el resultado
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificamos si se encontró el usuario
            if ($usuario) {
                // Comparamos la contraseña actual ingresada con el hash almacenado
                if (password_verify($contrasenaActual, $usuario['ContrasenaHash'])) {
                    // Preparamos la consulta para actualizar el token y la expiración del token
                    $updateQuery = "UPDATE USUARIO SET Token = ?, expiracion_token = DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE ID = ?";
                    $updateStmt = $this->conn->prepare($updateQuery);
                    $updateStmt->execute([$token, $id]);

                    // Verificamos si se actualizó el token
                    if ($updateStmt->rowCount() > 0) {
                        return ['success' => true, 'message' => 'Token actualizado con éxito.'];
                    } else {
                        return ['success' => false, 'error' => 'No se realizaron cambios en el token.'];
                    }
                } else {
                    return ['success' => false, 'error' => 'La contraseña actual es incorrecta.'];
                }
            } else {
                return ['success' => false, 'error' => 'No se encontró el usuario.'];
            }
        } catch (PDOException $e) {
            registrarError("Error al cambiar token: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al cambiar el token.'];
        }
    }

    /**
     * Cambia el correo electrónico de un usuario.
     *
     * Este método permite actualizar la dirección de correo electrónico de un usuario en la base de datos, 
     * también restableciendo el token y su fecha de expiración para garantizar la validez de los datos del usuario.
     *
     * Diseño:
     *
     * Entrada:
     *   - email (string, correo electrónico actual del usuario que desea cambiar).
     *   - nuevoCorreo (string, nuevo correo electrónico que se desea asignar al usuario).
     *
     * Proceso:
     *   1. Iniciar una transacción para asegurar la integridad de los cambios.
     *   2. Preparar y ejecutar una consulta SQL para actualizar el correo electrónico del usuario.
     *   3. Establecer el valor del token y la expiración a 0 para invalidar cualquier token asociado.
     *   4. Verificar si se actualizó el correo electrónico correctamente.
     *   5. Si la actualización es exitosa, confirmar la transacción y retornar un mensaje de éxito.
     *   6. Si no se realizaron cambios, revertir la transacción y retornar un mensaje de error.
     *   7. Registrar cualquier error ocurrido durante el proceso en un archivo de log.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": true,
     *       "message": "Correo electrónico actualizado con éxito."
     *     }
     *   - En caso de error:
     *     {
     *       "success": false,
     *       "error": "Motivo del error."
     *     }
     *
     * @param string $email El correo electrónico actual del usuario.
     * @param string $nuevoCorreo El nuevo correo electrónico que se desea asignar al usuario.
     * @return array Un array con un mensaje de éxito o error.
     */

    public function cambiarCorreo($email, $nuevoCorreo) {
        try {
            // Iniciar una transacción
            $this->conn->beginTransaction();
    
            // Preparamos la consulta para actualizar el correo
            $updateQuery = "UPDATE USUARIO SET Email = ?, token = 0, expiracion_token = 0 WHERE Email = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->execute([$nuevoCorreo, $email]);
    
            // Verificamos si se actualizó el correo
            if ($updateStmt->rowCount() > 0) {
                // Confirmar la transacción
                $this->conn->commit();
                return ['success' => true, 'message' => 'Correo electrónico actualizado con éxito.'];
            } else {
                // Revertir la transacción en caso de que no se realicen cambios
                $this->conn->rollBack();
                return ['success' => false, 'error' => 'No se realizaron cambios en el correo electrónico.'];
            }
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $this->conn->rollBack();
            registrarError("Error al actualizar el correo: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al actualizar el correo electrónico.'];
        }
    }
    
    /* ------------------------------------------------------------------------------------------
     *
     * METODOS ACCION RECUPERAR CONTRASEÑA
     * 
     *///----------------------------------------------------------------------------------------

    /**
     * Recupera la contraseña de un usuario a través de un token de recuperación.
     *
     * Este método permite a un usuario recuperar su contraseña utilizando un token de recuperación enviado previamente.
     * El token se valida y, si es válido y no ha expirado, se actualiza la contraseña con la nueva proporcionada por el usuario.
     * Además, se limpia el token y su fecha de expiración para garantizar la seguridad.
     *
     * Diseño:
     *
     * Entrada:
     *   - email (string, correo electrónico del usuario para recuperar la contraseña).
     *   - token (string, token de recuperación asociado al usuario).
     *   - nueva_contrasena (string, nueva contraseña que el usuario desea establecer).
     *
     * Proceso:
     *   1. Iniciar una transacción para asegurar que todas las operaciones se realicen de manera atómica.
     *   2. Consultar al usuario por su correo y verificar si existe.
     *   3. Si el usuario no existe, se retorna un error.
     *   4. Verificar si el token ha expirado comparando la fecha actual con la fecha de expiración del token.
     *      - Si el token ha expirado, se restablecen los valores del token y la fecha de expiración, y se notifica al usuario.
     *   5. Validar si el token proporcionado coincide con el almacenado.
     *   6. Si el token es válido, proceder a hashear la nueva contraseña y actualizarla en la base de datos.
     *   7. Si la actualización de la contraseña es exitosa, se confirma la transacción y se retorna un mensaje de éxito.
     *   8. Si la actualización no se realizó correctamente, se revierte la transacción y se retorna un mensaje de error.
     *   9. En caso de error durante el proceso, se revierte la transacción y se registra el error.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": "Contraseña actualizada con éxito."
     *     }
     *   - En caso de error:
     *     {
     *       "error": "Motivo del error."
     *     }
     *
     * @param string $email El correo electrónico del usuario que desea recuperar su contraseña.
     * @param string $token El token de recuperación asociado al usuario.
     * @param string $nueva_contrasena La nueva contraseña que el usuario desea establecer.
     * @return array Un array con un mensaje de éxito o error.
     */

    public function recuperarContrasena($email, $token, $nueva_contrasena) {
        try {
            // Iniciar transacción
            $this->conn->beginTransaction();
    
            // Consultamos al usuario utilizando el correo y el token de recuperación
            $query = "SELECT ID, token, expiracion_token FROM USUARIO WHERE Email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Verificamos si el usuario existe
            if (!$usuario) {
                return ['error' => 'Correo no encontrado.'];
            }
    
            // Verificamos si el token ha expirado
            $fecha_actual = new DateTime();
            $expiracion_token = new DateTime($usuario['expiracion_token']);
    
            if ($fecha_actual > $expiracion_token) {
                // Eliminar el token expirado (poner en 0 en lugar de NULL)
                $clearTokenQuery = "UPDATE USUARIO SET token = 0, expiracion_token = 0 WHERE ID = ?";
                $clearTokenStmt = $this->conn->prepare($clearTokenQuery);
                $clearTokenStmt->execute([$usuario['ID']]);
    
                // Confirmar transacción y retornar mensaje de error
                $this->conn->commit();
                return ['error' => 'El token ha expirado. Por favor, solicite uno nuevo.'];
            }
    
            // Comprobamos si el token es válido
            if ($usuario['token'] !== $token) {
                return ['error' => 'Token no válido.'];
            }
    
            $hashed_password = password_hash($nueva_contrasena, PASSWORD_BCRYPT);
    
            // Actualizamos la contraseña y limpiamos el token de recuperación
            $queryUpdate = "UPDATE USUARIO SET ContrasenaHash = ?, token = 0, expiracion_token = 0 WHERE Email = ?";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->execute([$hashed_password, $email]);
    
            // Verificamos si la actualización se realizó correctamente
            if ($stmtUpdate->rowCount() > 0) {
                // Confirmar transacción
                $this->conn->commit();
                return ['success' => 'Contraseña actualizada con éxito.'];
            } else {
                registrarError("No se pudo actualizar la contraseña para email: $email");
                $this->conn->rollBack();
                return ['error' => 'No se pudo actualizar la contraseña.'];
            }
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $this->conn->rollBack();
            // Registrar el error en el log
            registrarError("Error en recuperación de contraseña: " . $e->getMessage());
            return ['error' => 'Hubo un error al recuperar la contraseña.'];
        }
    }

    /**
     * Cambia la contraseña de un usuario por su correo electrónico.
     *
     * Este método permite a un usuario cambiar su contraseña utilizando su correo electrónico.
     * Primero, se verifica que el usuario exista en la base de datos y luego se actualiza la contraseña,
     * el token de recuperación y la expiración del token.
     * Si la contraseña no cambia, se notificará que no se realizaron cambios.
     *
     * Diseño:
     *
     * Entrada:
     *   - email (string, correo electrónico del usuario).
     *   - nuevaContrasena (string, nueva contraseña que el usuario desea establecer).
     *
     * Proceso:
     *   1. Consultar al usuario utilizando su correo electrónico para obtener su ID y el hash de la contraseña.
     *   2. Si el usuario no existe, se retorna un error indicando que no se encontró al usuario.
     *   3. Si el usuario existe, se genera un hash de la nueva contraseña.
     *   4. Se prepara la consulta para actualizar la contraseña del usuario, y se establece el token y la expiración a 0.
     *   5. Si se actualiza la contraseña correctamente, se retorna un mensaje de éxito.
     *   6. Si no se realizaron cambios (la contraseña ya es la misma), se retorna un mensaje de error.
     *   7. En caso de error, se registra el error y se retorna un mensaje indicando que hubo un problema al cambiar la contraseña.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": true,
     *       "message": "Contraseña y token actualizados con éxito."
     *     }
     *   - En caso de error:
     *     {
     *       "success": false,
     *       "error": "Motivo del error."
     *     }
     *
     * @param string $email El correo electrónico del usuario que desea cambiar su contraseña.
     * @param string $nuevaContrasena La nueva contraseña que el usuario desea establecer.
     * @return array Un array con un mensaje de éxito o error.
     */

    // Método para cambiar la contraseña de un usuario por correo electrónico
    public function cambiarContrasenaPorCorreo($email, $nuevaContrasena) {
        try {
            // Preparamos la consulta para obtener el ID y el hash de la contraseña actual del usuario
            $query = "SELECT ID, ContrasenaHash FROM USUARIO WHERE Email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);

            // Obtenemos el resultado
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificamos si se encontró el usuario
            if ($usuario) {
                // Hasheamos la nueva contraseña
                $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

                // Preparamos la consulta para actualizar la contraseña del usuario, el token y la expiración del token
                $updateQuery = "UPDATE USUARIO SET ContrasenaHash = ?, token = 0, expiracion_token = 0 WHERE Email = ?";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->execute([$contrasenaHash, $email]);

                // Verificamos si se actualizó la contraseña
                if ($updateStmt->rowCount() > 0) {
                    return ['success' => true, 'message' => 'Contraseña y token actualizados con éxito.'];
                } else {
                    return ['success' => false, 'error' => 'La contraseña ya es la misma o no se realizaron cambios.'];
                }
            } else {
                return ['success' => false, 'error' => 'No se encontró el usuario con ese correo electrónico.'];
            }
        } catch (PDOException $e) {
            registrarError("Error en cambiar contraseña: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al cambiar la contraseña.'];
        }
    }

    /**
 * Actualiza el token de recuperación para un usuario basado en su correo electrónico.
 *
 * Este método permite actualizar el token de recuperación de un usuario y su fecha de expiración.
 * Si el usuario está verificado y se encuentra en la base de datos, se genera un nuevo token 
 * y se actualiza en la base de datos con una expiración de 2 horas.
 * Si la actualización es exitosa, se devuelve un mensaje de éxito junto con los datos del usuario.
 *
 * Diseño:
 *
 * Entrada:
 *   - email (string, correo electrónico del usuario para actualizar el token de recuperación).
 *   - token (string, el nuevo token de recuperación generado).
 *
 * Proceso:
 *   1. Consultar al usuario en la base de datos utilizando su correo electrónico.
 *   2. Si el usuario no existe, se retorna un error indicando que el correo no fue encontrado.
 *   3. Verificar que el usuario esté verificado.
 *   4. Si el usuario está verificado, generar una nueva fecha de expiración para el token (2 horas).
 *   5. Actualizar el token y la expiración en la base de datos.
 *   6. Verificar si la actualización fue exitosa y registrar la información en el log.
 *   7. Si la actualización es exitosa, se devuelve un mensaje de éxito con los datos del usuario.
 *   8. Si ocurre un error en el proceso, se captura la excepción y se registra en el log.
 *
 * Salida:
 *   - En caso de éxito:
 *     {
 *       "success": "Token de recuperación registrado y actualizado con éxito.",
 *       "nombre": "Nombre del usuario",
 *       "apellidos": "Apellidos del usuario",
 *       "email": "Correo electrónico del usuario"
 *     }
 *   - En caso de error:
 *     {
 *       "error": "Motivo del error."
 *     }
 *
 * @param string $email El correo electrónico del usuario cuyo token de recuperación se actualizará.
 * @param string $token El nuevo token de recuperación generado.
 * @return array Un array con un mensaje de éxito o error.
 */

    public function actualizarTokenRecuperacion($email, $token) {
        try {
            registrarError("Iniciando la actualización del token de recuperación para el correo: $email");
    
            // Consultar al usuario por su correo electrónico
            $query = "SELECT ID, Nombre, Apellidos, Email, Verificado, token FROM USUARIO WHERE Email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Verificar si el usuario existe
            if (!$usuario) {
                registrarError("Correo no encontrado: $email");
                return ['error' => 'Correo no encontrado.'];
            }
    
            // Verificar si el usuario está verificado
            if ($usuario['Verificado'] != 1) {
                registrarError("El usuario con correo $email no está verificado.");
                return ['error' => 'El usuario no está verificado.'];
            }
    
            // Registrar si el usuario fue encontrado y está verificado
            registrarError("Usuario encontrado y verificado: " . json_encode($usuario));
    
            // Calcular nueva expiración (2 horas desde ahora)
            $nuevaExpiracion = (new DateTime())->add(new DateInterval('PT2H'))->format('Y-m-d H:i:s');
            registrarError("Nueva expiración calculada: $nuevaExpiracion");
    
            // Actualizar el token de recuperación y su expiración
            $queryUpdate = "UPDATE USUARIO SET token = ?, expiracion_token = ? WHERE Email = ?";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->execute([$token, $nuevaExpiracion, $email]);
    
            // Verificar si la actualización fue exitosa
            if ($stmtUpdate->rowCount() > 0) {
                // Registrar éxito de la actualización
                registrarError("Token de recuperación actualizado correctamente para el correo: $email");
                registrarError("Token de recuperación actualizado correctamente para el correo: $token");

            } else {
                // Si no se actualizó ninguna fila, registrar información sobre ello
                registrarError("No se actualizó el token de recuperación para el correo: $email");
            }
    
            // Devolver éxito con los datos del usuario
            return [
                'success' => 'Token de recuperación registrado y actualizado con éxito.',
                'nombre' => $usuario['Nombre'],
                'apellidos' => $usuario['Apellidos'],
                'email' => $usuario['Email']  // Incluir el correo electrónico
            ];
        } catch (PDOException $e) {
            // Registrar el error en el log
            registrarError("Error al actualizar el token de recuperación: " . $e->getMessage());
            return ['error' => 'Hubo un error al registrar el token de recuperación.'];
        }
    }
    
    /* ------------------------------------------------------------------------------------------
     *
     * METODOS ACCION UNIVERSALES
     * 
     *///----------------------------------------------------------------------------------------
    

    /**
     * Actualiza el token de huella para un usuario basado en su ID.
     *
     * Este método permite subir el token de huella para un usuario, utilizando su ID. Si el usuario existe,
     * se actualiza el token de huella en la base de datos.
     *
     * Diseño:
     *
     * Entrada:
     *   - id (int, el ID del usuario para actualizar el token de huella).
     *   - tokenHuella (string, el nuevo token de huella que se va a asignar al usuario).
     *
     * Proceso:
     *   1. Consultar al usuario en la base de datos utilizando su ID.
     *   2. Si el usuario no existe, se retorna un error indicando que no se encontró el usuario.
     *   3. Si el usuario existe, se actualiza el token de huella con el valor proporcionado.
     *   4. Verificar si la actualización fue exitosa y retornar un mensaje de éxito o error según corresponda.
     *   5. Si ocurre un error en el proceso, se captura la excepción y se registra en el log.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": true,
     *       "message": "Token de huella actualizado con éxito."
     *     }
     *   - En caso de error:
     *     {
     *       "success": false,
     *       "error": "Motivo del error."
     *     }
     *
     * @param int $id El ID del usuario cuyo token de huella se actualizará.
     * @param string $tokenHuella El nuevo token de huella generado.
     * @return array Un array con un mensaje de éxito o error.
     */

    public function subirTokenHuella($id, $tokenHuella) {
        try {
            // Preparamos la consulta para verificar si el usuario existe
            $query = "SELECT ID FROM USUARIO WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
    
            // Verificamos si se encontró el usuario
            if ($stmt->rowCount() > 0) {
                // Preparamos la consulta para actualizar el token de la huella
                $updateQuery = "UPDATE USUARIO SET token_huella = ? WHERE ID = ?";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->execute([$tokenHuella, $id]);
    
                // Verificamos si se actualizó el token de la huella
                if ($updateStmt->rowCount() > 0) {
                    return ['success' => true, 'message' => 'Token de huella actualizado con éxito.'];
                } else {
                    return ['success' => false, 'error' => 'No se realizaron cambios o el token es el mismo.'];
                }
            } else {
                return ['success' => false, 'error' => 'No se encontró el usuario con el ID proporcionado.'];
            }
        } catch (PDOException $e) {
            registrarError("Error en subir token de huella: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al actualizar el token de huella.'];
        }
    }

    /**
     * Registra un nuevo administrador en el sistema.
     *
     * Este método permite registrar un nuevo usuario con rol de administrador en la base de datos. 
     * Se le asigna el rol de administrador automáticamente (ROL_RolID = 1) y se garantiza que el 
     * campo de verificación está marcado como '1' (verificado).
     *
     * Diseño:
     *
     * Entrada:
     *   - nombre (string, el nombre del administrador).
     *   - apellidos (string, los apellidos del administrador).
     *   - email (string, el correo electrónico del administrador).
     *   - contrasenaHash (string, la contraseña del administrador, ya hasheada).
     *
     * Proceso:
     *   1. Se prepara la consulta para insertar los datos del administrador en la tabla `USUARIO`.
     *   2. Los campos `token`, `expiracion_token` y `token_huella` se dejan como `NULL` por defecto.
     *   3. Se asigna el valor de `ROL_RolID` como `1`, indicando que el usuario es un administrador.
     *   4. Si la inserción es exitosa, se retorna un mensaje de éxito.
     *   5. Si ocurre un error en el proceso, se captura la excepción y se registra en el log.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": "Administrador añadido con éxito."
     *     }
     *   - En caso de error:
     *     {
     *       "error": "Error al insertar un administrador."
     *     }
     *
     * @param string $nombre El nombre del administrador.
     * @param string $apellidos Los apellidos del administrador.
     * @param string $email El correo electrónico del administrador.
     * @param string $contrasenaHash La contraseña hasheada del administrador.
     * @return array Un array con un mensaje de éxito o error.
     */

    public function registrarAdmin($nombre, $apellidos, $email, $contrasenaHash) {
        try {
            $rol_rolid = 1;
    
            // Query para insertar el administrador con valores NULL donde sea posible
            $query = "INSERT INTO USUARIO 
                      (Nombre, Apellidos, Email, ContrasenaHash, Verificado, token, expiracion_token, token_huella, ROL_RolID) 
                      VALUES (?, ?, ?, ?, 1, NULL, NULL, NULL, ?)";
    
            // Preparar y ejecutar la consulta
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nombre, $apellidos, $email, $contrasenaHash, $rol_rolid]);
    
            // Retornar éxito
            return ['success' => 'Administrador añadido con éxito.'];
        } catch (PDOException $e) {
            // Registrar el error
            registrarError("Error en insertar administrador: " . $e->getMessage() . "\n");
            return ['error' => 'Error al insertar un administrador.'];
        }
    }
    
    /**
     * Elimina un usuario y su(s) sensor(es) asociado(s) de la base de datos.
     *
     * Este método permite eliminar un usuario de la tabla `USUARIO`, y debido a la configuración de
     * la base de datos (ON DELETE CASCADE), los sensores asociados también se eliminarán automáticamente.
     * Si el usuario no se encuentra, o si no se puede eliminar, se registra un error y se retorna un mensaje adecuado.
     *
     * Diseño:
     *
     * Entrada:
     *   - idUsuario (int, el ID del usuario que se desea eliminar).
     *
     * Proceso:
     *   1. Se verifica si el usuario existe en la base de datos con el ID proporcionado.
     *   2. Si el usuario existe, se procede a eliminarlo y a eliminar los sensores asociados de manera automática.
     *   3. Si la eliminación es exitosa, se retorna un mensaje de éxito.
     *   4. Si no se puede eliminar el usuario o no se encuentra, se retorna un mensaje de error.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": true,
     *       "message": "Usuario y sensor(es) asociados eliminados con éxito."
     *     }
     *   - En caso de error:
     *     {
     *       "success": false,
     *       "error": "No se pudo eliminar el usuario."
     *     }
     *     o
     *     {
     *       "success": false,
     *       "error": "No se encontró el usuario con ese ID."
     *     }
     *
     * @param int $idUsuario El ID del usuario que se desea eliminar.
     * @return array Un array con un mensaje de éxito o error.
     */

    // Método para eliminar un usuario y su sensor asociado
    public function eliminarUsuario($idUsuario) {
        try {
            // Preparamos la consulta para verificar si el usuario existe
            $query = "SELECT ID FROM USUARIO WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$idUsuario]);

            // Obtenemos el resultado
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificamos si se encontró el usuario
            if ($usuario) {
                // Eliminamos el usuario (los sensores se eliminan automáticamente por ON DELETE CASCADE)
                $deleteUsuarioQuery = "DELETE FROM USUARIO WHERE ID = ?";
                $deleteUsuarioStmt = $this->conn->prepare($deleteUsuarioQuery);
                $deleteUsuarioStmt->execute([$idUsuario]);

                // Verificamos si se eliminó el usuario
                if ($deleteUsuarioStmt->rowCount() > 0) {
                    return ['success' => true, 'message' => 'Usuario y sensor(es) asociados eliminados con éxito.'];
                } else {
                    return ['success' => false, 'error' => 'No se pudo eliminar el usuario.'];
                }
            } else {
                return ['success' => false, 'error' => 'No se encontró el usuario con ese ID.'];
            }
        } catch (PDOException $e) {
            registrarError("Error en eliminar usuario: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al eliminar el usuario.'];
        }
    }

}