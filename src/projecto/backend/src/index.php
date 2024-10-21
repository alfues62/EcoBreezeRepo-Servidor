<?php
require_once 'db/conexion.php';

class Acciones {
    private $conexion;

    public function __construct() {
        $this->conexion = (new Conexion())->getConnection();
    }

    public function obtenerAcciones() {
        $query = "SELECT * FROM acciones";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarAccion($numero) {
        $query = "INSERT INTO acciones (numero) VALUES (:numero)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':numero', $numero);
        return $stmt->execute();
    }

    public function eliminarAccion($id) {
        $query = "DELETE FROM acciones WHERE id = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function obtenerAccionPorId($id) {
        $query = "SELECT * FROM acciones WHERE id = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function editarAccion($id, $numero) {
        $query = "UPDATE acciones SET numero = :numero WHERE id = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

$acciones = new Acciones();
$listaAcciones = $acciones->obtenerAcciones();

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['insertar'])) {
        $numero = $_POST['numero'];
        $acciones->insertarAccion($numero);
        header('Location: index.php');
    } elseif (isset($_POST['eliminar'])) {
        $id = $_POST['id'];
        $acciones->eliminarAccion($id);
        header('Location: index.php');
    } elseif (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $numero = $_POST['numero'];
        $acciones->editarAccion($id, $numero);
        header('Location: index.php');
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acciones</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Lista de Acciones</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listaAcciones as $accion): ?>
                <tr>
                    <td><?php echo $accion['id']; ?></td>
                    <td><?php echo $accion['numero']; ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $accion['id']; ?>">
                            <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal<?php echo $accion['id']; ?>">Editar</button>

                        <!-- Modal para editar acción -->
                        <div class="modal fade" id="editModal<?php echo $accion['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Editar Acción</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post">
                                            <input type="hidden" name="id" value="<?php echo $accion['id']; ?>">
                                            <div class="form-group">
                                                <label for="numero">Número</label>
                                                <input type="number" class="form-control" name="numero" value="<?php echo $accion['numero']; ?>" required>
                                            </div>
                                            <button type="submit" name="editar" class="btn btn-primary">Guardar cambios</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="mt-5">Agregar Nueva Acción</h2>
    <form method="post">
        <div class="form-group">
            <label for="numero">Número</label>
            <input type="number" class="form-control" name="numero" required>
        </div>
        <button type="submit" name="insertar" class="btn btn-success">Agregar Acción</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
