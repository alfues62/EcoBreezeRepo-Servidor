<?php

require_once '../db/conexion.php';

class UsuariosCRUD {
    private $conn;

    public function __construct() {
        // Crea una nueva instancia de la conexión a la base de datos
        $this->conn = (new Conexion())->getConnection(); // Asegúrate de que esta clase exista y devuelva una conexión válida
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
            // Manejo de errores
            error_log("Error en leer usuarios: " . $e->getMessage());
            return ['error' => 'Error al obtener usuarios'];
        }
    }

    // Método para insertar un nuevo usuario
    public function insertar($nombre, $apellidos, $email, $contrasenaHash, $tfa_secret = null) {
        try {
            // Establecer el rol como 2
            $rol_rolid = 2;

            // Generar un token único para la verificación
            $token = bin2hex(random_bytes(16));

            $query = "INSERT INTO USUARIO (Nombre, Apellidos, Email, ContrasenaHash, TFA_Secret, Verificado, TokenVerificacion, ROL_RolID) 
                      VALUES (?, ?, ?, ?, ?, 0, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nombre, $apellidos, $email, $contrasenaHash, $tfa_secret, $token, $rol_rolid]);

            return ['success' => 'Usuario insertado con éxito. Por favor verifica tu correo.'];
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error en insertar usuario: " . $e->getMessage());
            return ['error' => 'Error al insertar usuario'];
        }
    }

    // Método para editar un usuario existente
    public function editar($id, $nombre, $apellidos, $email, $contrasenaHash, $rol_rolid, $tfa_secret = null) {
        try {
            $query = "UPDATE USUARIO SET Nombre = ?, Apellidos = ?, Email = ?, ContrasenaHash = ?, ROL_RolID = ?, TFA_Secret = ? WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nombre, $apellidos, $email, $contrasenaHash, $rol_rolid, $tfa_secret, $id]);
            return ['success' => 'Usuario actualizado con éxito'];
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error en editar usuario: " . $e->getMessage());
            return ['error' => 'Error al editar usuario'];
        }
    }

    // Método para eliminar un usuario
    public function borrar($id) {
        try {
            $query = "DELETE FROM USUARIO WHERE ID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return ['success' => 'Usuario eliminado con éxito'];
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error en borrar usuario: " . $e->getMessage());
            return ['error' => 'Error al eliminar usuario'];
        }
    }

    // Método para verificar el correo electrónico mediante el token
    public function verificarEmail($token) {
        try {
            $query = "UPDATE USUARIO SET Verificado = 1, TokenVerificacion = NULL WHERE TokenVerificacion = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$token]);
    
            return $stmt->rowCount() > 0; // Retorna true si se actualizó un registro
        } catch (PDOException $e) {
            error_log("Error en verificar email: " . $e->getMessage());
            return false; // Retorna false en caso de error
        }
    }
}
