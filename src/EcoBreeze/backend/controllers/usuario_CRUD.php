<?php 
require_once(__DIR__ . '/../../db/conexion.php');

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
        $stmt->execute([$nombre, $apellidos, $email, $contrasenaHash, $token_verificacion, $usuarioExistente ? $usuarioExistente['expiracion_token'] : null, $rol_rolid]);

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
    
    public function verificarConHuellaYCorreo($email, $token_huella) {
        try {
            // Prepara la consulta para verificar el email y el token de huella
            $stmt = $this->conn->prepare("SELECT ID, Nombre, ROL_RolID FROM USUARIO WHERE Email = :email AND token_huella = :token_huella");
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
            error_log("Error al verificar email y token de huella: " . $e->getMessage(), 3, $this->logFile);
            return ['success' => false, 'error' => 'Error al verificar email y token de huella'];
        }
    }

    public function obtenerTokenHuellaPorCorreo($email) {
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
            error_log("Error al obtener el token de huella: " . $e->getMessage(), 3, $this->logFile);
            return null;
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
        error_log("Error en subir token de huella: " . $e->getMessage() . "\n", 3, $this->logFile);
        return ['success' => false, 'error' => 'Error al actualizar el token de huella.'];
    }
}

public function recuperar_contrasena($email, $token, $nueva_contrasena) {
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
            // Eliminar el token expirado
            $clearTokenQuery = "UPDATE USUARIO SET token_recuperacion = NULL, expiracion_token = NULL WHERE ID = ?";
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

        // Hasheamos la nueva contraseña
        $hashed_password = password_hash($nueva_contrasena, PASSWORD_BCRYPT);

        // Actualizamos la contraseña y limpiamos el token de recuperación
        $queryUpdate = "UPDATE USUARIO SET ContrasenaHash = ?, token_recuperacion = NULL, expiracion_token = NULL WHERE Email = ?";
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

}
?>
