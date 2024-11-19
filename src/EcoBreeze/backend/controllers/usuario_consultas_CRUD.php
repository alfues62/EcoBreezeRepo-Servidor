<?php 
require_once(__DIR__ . '/../../db/conexion.php');

class UsuariosConsultasCRUD {
    private $conn;
    private $logFile; 

    // Modificación del constructor para aceptar la conexión existente y definir el archivo de log
    public function __construct($conn) {
        $this->conn = $conn; // Usar la conexión pasada como argumento
        date_default_timezone_set('Europe/Madrid'); // Establecer la zona horaria
        $this->logFile = '/var/www/html/logs/app.log'; // Establecer la ruta del archivo de log
    }

    /* ------------------------------------------------------------------------------------------
     * 
     * METODOS CONSULTAS LOGIN
     * 
     *///----------------------------------------------------------------------------------------
    public function login($email, $contrasena) {
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
    public function loginHuella($email, $token_huella) {
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
            error_log("Error al obtener el token de huella: " . $e->getMessage(), 3, $this->logFile);
            return null;
        }
    }
    /* ------------------------------------------------------------------------------------------
     *
     * METODOS CONSULTAS UNIVERSALES
     * 
     *///----------------------------------------------------------------------------------------
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
}
?>
