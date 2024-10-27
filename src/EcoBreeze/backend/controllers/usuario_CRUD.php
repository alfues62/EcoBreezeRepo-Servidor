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
    public function insertar($nombre, $apellidos, $email, $contrasenaHash, $tfa_secret = null) {
        try {
            $rol_rolid = 2; // Establecer el rol como 2
            $token = bin2hex(random_bytes(16)); // Generar un token único

            $query = "INSERT INTO USUARIO (Nombre, Apellidos, Email, ContrasenaHash, TFA_Secret, Verificado, TokenVerificacion, ROL_RolID) 
                      VALUES (?, ?, ?, ?, ?, 0, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nombre, $apellidos, $email, $contrasenaHash, $tfa_secret, $token, $rol_rolid]);

            return ['success' => 'Usuario insertado con éxito. Por favor verifica tu correo.'];
        } catch (PDOException $e) {
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

    // Método para verificar las credenciales de inicio de sesión
    public function verificarCredencialesCompleto($email, $contrasena) {
    try {
        // Verificar si el email está registrado
        $stmt = $this->conn->prepare("SELECT ID, Nombre, ROL_RolID, ContrasenaHash FROM USUARIO WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si el usuario no existe, devolver error específico
        if (!$usuario) {
            return ['error' => 'Correo no registrado'];
        }

        // Si el usuario existe, verificar la contraseña
        if (password_verify($contrasena, $usuario['ContrasenaHash'])) {
            return [
                'ID' => $usuario['ID'],
                'Nombre' => $usuario['Nombre'],
                'Rol' => $usuario['ROL_RolID']
            ]; // Devuelve los datos del usuario si las credenciales son correctas
        } else {
            return ['error' => 'Contraseña incorrecta'];
        }
    } catch (PDOException $e) {
        error_log("Error al verificar credenciales: " . $e->getMessage() . "\n", 3, $this->logFile);
        return ['error' => 'Error al verificar las credenciales'];
    }
}
// Método para verificar si el email ya está registrado
public function emailExistente($email) {
    try {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM USUARIO WHERE Email = ?");
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

}
?>
