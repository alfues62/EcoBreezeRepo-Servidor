<?php 
require_once(__DIR__ . '/../../db/conexion.php');

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
            registrarError("Error en cambiar correo: " . $e->getMessage() . "\n");
            return ['success' => false, 'error' => 'Error al cambiar el correo electrónico.'];
        }
    }
    /* ------------------------------------------------------------------------------------------
     *
     * METODOS ACCION RECUPERAR CONTRASEÑA
     * 
     *///----------------------------------------------------------------------------------------
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