<?php 
require_once(__DIR__ . '/../../db/conexion.php');
require '../log.php';

class UsuariosCRUD {
    private $conn;
    private $logFile; 

    // Modificación del constructor para aceptar la conexión existente y definir el archivo de log
    public function __construct($conn) {
        $this->conn = $conn; // Usar la conexión pasada como argumento
        date_default_timezone_set('Europe/Madrid'); // Establecer la zona horaria
        $this->logFile = '/var/www/html/logs/app.log'; // Establecer la ruta del archivo de log
    }

    // Método para obtener usuarios
    public function leer($id = null, $nombre = null, $apellidos = null, $email = null) {
        try {
            $query = "SELECT * FROM USUARIO WHERE 1=1";
            $params = [];

            if ($id) {
                $query .= " AND ID = ?";
                $params[] = $id;
            }
            if ($nombre) {
                $query .= " AND Nombre LIKE ?";
                $params[] = "%$nombre%";
            }
            if ($apellidos) {
                $query .= " AND Apellidos LIKE ?";
                $params[] = "%$apellidos%";
            }
            if ($email) {
                $query .= " AND Email = ?";
                $params[] = $email;
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error en leer usuarios: " . $e->getMessage() . "\n", 3, $this->logFile);
            return ['error' => 'Error al filtrar busqueda'];
        }
    }
// Método para insertar un nuevo usuario
public function insertar($nombre, $apellidos, $email, $contrasenaHash, $token_verificacion) {
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
        $query = "INSERT INTO USUARIO (Nombre, Apellidos, Email, ContrasenaHash, Verificado, TokenVerificacion, expiracion_token, ROL_RolID) 
                  VALUES (?, ?, ?, ?, 0, ?, ?, ?)";

        // Preparar y ejecutar la consulta
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$nombre, $apellidos, $email, $contrasenaHash, $token_verificacion, $fechaExpiracionToken->format('Y-m-d H:i:s'), $rol_rolid]);

        // Retornar éxito
        return ['success' => 'Usuario insertado con éxito. Por favor verifica tu correo.'];
    } catch (PDOException $e) {
        // Registrar el error
        error_log("Error en insertar usuario: " . $e->getMessage() . "\n", 3, $this->logFile);
        return ['error' => 'Error al insertar un usuario'];
    }
}



    // Método para editar un usuario existente
    public function editar($id, $nombre, $apellidos, $email, $contrasenaHash, $rol_rolid, $tfa_secret = null) {
        try {
            $query = "UPDATE USUARIO SET Nombre = ?, Apellidos = ?, Email = ?, ContrasenaHash = ?, TFA_Secret = ?, ROL_RolID = ? WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nombre, $apellidos, $email, $contrasenaHash, $tfa_secret, $rol_rolid, $id]);

            return ['success' => 'Usuario actualizado con éxito.'];
        } catch (PDOException $e) {
            error_log("Error en editar usuario: " . $e->getMessage() . "\n", 3, $this->logFile);
            return ['error' => 'Error al editar usuario']; 
        }
    }

    // Método para borrar un usuario
    public function borrar($id) {
        try {
            $query = "DELETE FROM USUARIO WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);

            return ['success' => 'Usuario eliminado con éxito.'];
        } catch (PDOException $e) {
            error_log("Error en borrar usuario: " . $e->getMessage() . "\n", 3, $this->logFile);
            return null; // Manejar errores durante la eliminación
        }
    }

    public function verificarCredencialesCompleto($email, $contrasena) {
        try {
            // Verificar si el email está registrado
            $stmt = $this->conn->prepare("SELECT ID, Nombre, ROL_RolID, ContrasenaHash, Verificado, expiracion_token FROM USUARIO WHERE Email = :email");
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
                        'Rol' => $usuario['ROL_RolID'] // Aquí se obtiene el rol del usuario
                    ]
                ]; // Devuelve los datos del usuario si las credenciales son correctas
            } else {
                return ['success' => false, 'error' => 'Contraseña incorrecta'];
            }
        } catch (PDOException $e) {
            error_log("Error al verificar credenciales: " . $e->getMessage() . "\n", 3, $this->logFile);
            return ['success' => false, 'error' => 'Error al verificar las credenciales'];
        }
    }
    
    
// Método para verificar si el email ya está registrado
public function emailExistente($email) {
    try {
        $stmt = $this->conn->prepare("SELECT 1 FROM USUARIO WHERE Email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();
        return $count > 0; // Devuelve true si el email ya está registrado
    } catch (PDOException $e) {
        error_log("Error al verificar si el email existe: " . $e->getMessage() . "\n", 3, $this->logFile);
        return false; // Manejar errores durante la verificación
    }
}

// Método para insertar un nuevo sensor
public function insertarSensor($usuarioID, $mac) {
    try {
        $query = "INSERT INTO SENSOR (USUARIO_ID, MAC) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuarioID, $mac]);

        return ['success' => 'Sensor insertado con éxito.'];
    } catch (PDOException $e) {
        error_log("Error en insertar sensor: " . $e->getMessage() . "\n", 3, $this->logFile);
        return ['error' => 'Error al insertar el sensor'];
    }
}


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
        error_log("Error en obtener datos usuario: " . $e->getMessage() . "\n", 3, $this->logFile);
        return ['success' => false, 'error' => 'Error al obtener los datos del usuario.'];
    }
}


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
        error_log("Error en cambiar contraseña: " . $e->getMessage() . "\n", 3, $this->logFile);
        return ['success' => false, 'error' => 'Error al cambiar la contraseña.'];
    }
}

public function cambiarCorreoPorID($id, $contrasenaActual, $nuevoCorreo) {
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
                // Preparamos la consulta para actualizar el correo del usuario
                $updateQuery = "UPDATE USUARIO SET Email = ? WHERE ID = ?";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->execute([$nuevoCorreo, $id]);

                // Verificamos si se actualizó el correo
                if ($updateStmt->rowCount() > 0) {
                    return ['success' => true, 'message' => 'Correo electrónico actualizado con éxito.'];
                } else {
                    return ['success' => false, 'error' => 'El correo electrónico ya es el mismo o no se realizaron cambios.'];
                }
            } else {
                return ['success' => false, 'error' => 'La contraseña actual es incorrecta.'];
            }
        } else {
            return ['success' => false, 'error' => 'No se encontró el usuario.'];
        }
    } catch (PDOException $e) {
        error_log("Error en cambiar correo: " . $e->getMessage() . "\n", 3, $this->logFile);
        return ['success' => false, 'error' => 'Error al cambiar el correo electrónico.'];
    }
}


public function verificar_correo($email, $token) {
    try {
        // Iniciar transacción
        $this->conn->beginTransaction();

        // Consultamos al usuario utilizando el correo y el token de verificación
        $query = "SELECT ID, TokenVerificacion, Verificado, expiracion_token FROM USUARIO WHERE Email = ?";
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
        if ($usuario['TokenVerificacion'] !== $token) {
            return ['error' => 'Token no válido.'];
        }

        // Si el token es válido y no ha expirado, verificamos al usuario
        $queryUpdate = "UPDATE USUARIO SET Verificado = 1, TokenVerificacion = 0, expiracion_token = null WHERE Email = ?";
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

    public function obtenerDatosUsuarioPorEmail($email) {
        try {
            // Consultamos al usuario utilizando el correo
            $query = "SELECT ID, Nombre, Apellidos, Email FROM USUARIO WHERE Email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificamos si el usuario existe
            if (!$usuario) {
                return ['error' => 'Correo no encontrado.'];
            }

            // Retornamos los datos del usuario
            return ['success' => true, 'usuario' => $usuario];
        } catch (PDOException $e) {
            // Registrar el error en el log
            registrarError("Error al obtener datos del usuario por correo: " . $e->getMessage());
            return ['error' => 'Hubo un error al obtener los datos del usuario.'];
        }
    }

    public function actualizarTokenRecuperacion($email, $token) {
        try {
            registrarError("Iniciando la actualización del token de recuperación para el correo: $email");
    
            // Consultar al usuario por su correo electrónico
            $query = "SELECT ID, Nombre, Apellidos, Email, Verificado FROM USUARIO WHERE Email = ?";
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
            $queryUpdate = "UPDATE USUARIO SET token_recuperacion = ?, expiracion_recuperacion = ? WHERE Email = ?";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->execute([$token, $nuevaExpiracion, $email]);
    
            // Verificar si la actualización fue exitosa
            if ($stmtUpdate->rowCount() > 0) {
                // Registrar éxito de la actualización
                registrarError("Token de recuperación actualizado correctamente para el correo: $email");
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
    public function recuperarContrasena($email, $token, $nueva_contrasena) {
        try {
            // Iniciar transacción
            $this->conn->beginTransaction();
    
            // Consultamos al usuario utilizando el correo y el token de recuperación
            $query = "SELECT ID, token_recuperacion, expiracion_token FROM USUARIO WHERE Email = ?";
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
                $clearTokenQuery = "UPDATE USUARIO SET token_recuperacion = 0, expiracion_token = 0 WHERE ID = ?";
                $clearTokenStmt = $this->conn->prepare($clearTokenQuery);
                $clearTokenStmt->execute([$usuario['ID']]);
    
                // Confirmar transacción y retornar mensaje de error
                $this->conn->commit();
                return ['error' => 'El token ha expirado. Por favor, solicite uno nuevo.'];
            }
    
            // Comprobamos si el token es válido
            if ($usuario['token_recuperacion'] !== $token) {
                return ['error' => 'Token no válido.'];
            }
    
            $hashed_password = password_hash($nueva_contrasena, PASSWORD_BCRYPT);
    
            // Actualizamos la contraseña y limpiamos el token de recuperación
            $queryUpdate = "UPDATE USUARIO SET ContrasenaHash = ?, token_recuperacion = 0, expiracion_token = 0 WHERE Email = ?";
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
    
    public function verificarTokenValido($email, $token) {
        try {
            // Consulta SQL para verificar el token y la fecha de expiración
            $query = "SELECT token_recuperacion, expiracion_token 
                      FROM USUARIO 
                      WHERE Email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Verificar si el usuario existe
            if (!$usuario) {
                return [
                    'success' => false,
                    'error' => 'Correo no encontrado.'
                ];
            }
    
            // Verificar si el token es "0"
            if ($usuario['token_recuperacion'] === '0') {
                return [
                    'success' => false,
                    'error' => 'El token no es válido. Por favor, solicite uno nuevo.'
                ];
            }
    
            // Verificar si el token coincide
            if ($usuario['token_recuperacion'] !== $token) {
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
    
            // Retornar éxito si todas las verificaciones pasan
            return [
                'success' => true,
                'message' => 'El token es válido y no ha expirado.'
            ];
        } catch (PDOException $e) {
            // Registrar el error en el log
            registrarError("Error al verificar el token: " . $e->getMessage());
    
            // Retornar un mensaje de error genérico
            return [
                'success' => false,
                'error' => 'Hubo un error al verificar el token.'
            ];
        }
    }
    
    public function marcarTokenComoUtilizado($email) {
        try {
            // Consulta SQL para actualizar el token y la fecha de expiración
            $query = "UPDATE USUARIO 
                      SET token_recuperacion = '0', expiracion_token = '0' 
                      WHERE Email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
    
            // Verificar si se actualizó algún registro
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'El token ha sido marcado como utilizado.'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'No se encontró el usuario o el token ya fue utilizado.'
                ];
            }
        } catch (PDOException $e) {
            // Registrar el error en el log
            registrarError("Error al marcar el token como utilizado: " . $e->getMessage());
    
            // Retornar un mensaje de error genérico
            return [
                'success' => false,
                'error' => 'Hubo un error al marcar el token como utilizado.'
            ];
        }
    }
    
    
}
?>
