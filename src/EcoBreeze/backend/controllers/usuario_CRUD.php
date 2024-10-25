<?php 
require_once(__DIR__ . '/../../db/conexion.php');

class UsuariosCRUD {
    private $conn;
    private $logFile; // Propiedad para el archivo de log

    // Modificación del constructor para aceptar la conexión existente y definir el archivo de log
    public function __construct($conn) {
        $this->conn = $conn; // Usar la conexión pasada como argumento
        $this->logFile = __DIR__ . '/../../logs/app.log'; // Establecer la ruta del archivo de log
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en leer usuarios: " . $e->getMessage() . "\n", 3, $this->logFile);
            return ['error' => 'Error al obtener usuarios'];
        }
    }

    // Método para insertar un nuevo usuario
    public function insertar($nombre, $apellidos, $email, $contrasenaHash, $tfa_secret = null) {
        try {
            // Registro de datos que se intentan insertar
            error_log("Datos para insertar: Nombre: $nombre, Apellidos: $apellidos, Email: $email", 3, $this->logFile);

            $rol_rolid = 2; // Establecer el rol como 2
            $token = bin2hex(random_bytes(16)); // Generar un token único

            $query = "INSERT INTO USUARIO (Nombre, Apellidos, Email, ContrasenaHash, TFA_Secret, Verificado, TokenVerificacion, ROL_RolID) 
                      VALUES (?, ?, ?, ?, ?, 0, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nombre, $apellidos, $email, $contrasenaHash, $tfa_secret, $token, $rol_rolid]);

            return ['success' => 'Usuario insertado con éxito. Por favor verifica tu correo.'];
        } catch (PDOException $e) {
            error_log("Error en insertar usuario: " . $e->getMessage() . "\n", 3, $this->logFile);
            return ['error' => 'Error al insertar usuario: ' . $e->getMessage()]; // Mensaje más detallado
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
            return ['error' => 'Error al editar usuario: ' . $e->getMessage()];
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
            return ['error' => 'Error al eliminar usuario: ' . $e->getMessage()];
        }
    }

    // Método para verificar las credenciales de inicio de sesión
public function verificarCredenciales($email, $contrasena) {
    try {
        // Preparar la consulta para verificar las credenciales
        $stmt = $this->conn->prepare("SELECT ID, Nombre, ROL_RolID, ContrasenaHash, TFA_Secret FROM USUARIO WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar la contraseña
        if ($usuario && password_verify($contrasena, $usuario['ContrasenaHash'])) {
            // Asegúrate de que el rol esté en el resultado
            return [
                'ID' => $usuario['ID'],
                'Nombre' => $usuario['Nombre'],
                'Rol' => $usuario['ROL_RolID'] // Incluye el rol aquí
            ]; // Devuelve los datos del usuario si las credenciales son correctas
        }

        return null; // Devuelve null si no coincide
    } catch (PDOException $e) {
        error_log("Error al verificar credenciales: " . $e->getMessage() . "\n", 3, $this->logFile);
        return null; // Manejar errores durante la verificación
    }
}
public function verificarUsuarioPorEmail($email) {
    // Consulta a la base de datos para verificar si el email está registrado
    $stmt = $this->conn->prepare("SELECT * FROM USUARIO WHERE Email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna el usuario si existe, o null si no
}
    
}
?>
