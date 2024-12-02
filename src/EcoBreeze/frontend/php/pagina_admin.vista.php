<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios y Mediciones</title>
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/pagina_admin.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/frontend/index.php">Mi Aplicación</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#" id="ultimaMedicionBtn">Última Medición</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="eliminarUsuarioBtn">Eliminar Usuario</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/backend/logout.php">Cerrar Sesión</a>
            </li>
        </ul>
    </div>
</nav>

<h1>Usuarios</h1>

<?php if ($success_message): ?>
    <p class="success"><?php echo $success_message; ?></p>
<?php endif; ?>

<?php if ($error_message): ?>
    <p class="error"><?php echo $error_message; ?></p>
<?php endif; ?>

<?php if (isset($usuarios) && is_array($usuarios) && count($usuarios) > 0): ?>
    <table class="table">
        <thead>
            <tr>
                <th>
                    ID
                    <i class="bi bi-caret-up-fill cursor-pointer"></i>
                    <i class="bi bi-caret-down-fill cursor-pointer"></i>
                </th>
                <th>
                    Nombre
                    <i class="bi bi-caret-up-fill cursor-pointer"></i>
                    <i class="bi bi-caret-down-fill cursor-pointer"></i>
                </th>
                <th>
                    Apellidos
                    <i class="bi bi-caret-up-fill cursor-pointer"></i>
                    <i class="bi bi-caret-down-fill cursor-pointer"></i>
                </th>
                <th>
                    Email
                    <i class="bi bi-caret-up-fill cursor-pointer"></i>
                    <i class="bi bi-caret-down-fill cursor-pointer"></i>
                </th>
                <th>
                    Última Medición
                    <i class="bi bi-caret-up-fill cursor-pointer"></i>
                    <i class="bi bi-caret-down-fill cursor-pointer"></i>
                </th>
            </tr>
        </thead>
        <tbody id="usuariosTableBody">
            <?php foreach ($usuarios as $index => $usuario): ?>
                <tr class="usuarioRow" data-index="<?php echo $index; ?>">
                    <td><?php echo htmlspecialchars($usuario['ID']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['Nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['Apellidos']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['Email']); ?></td>
                    <td>
                        <?php
                        // Mostrar "N/A" si la última medición es "N/A"
                        if ($usuario['UltimaMedicion'] === 'N/A') {
                            echo "<span class='na'>N/A</span>";
                        } else {
                            echo htmlspecialchars($usuario['UltimaMedicion']);
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <nav>
        <ul class="pagination" id="pagination">
            <!-- Se llenará con la paginación en JavaScript -->
        </ul>
    </nav>

<?php else: ?>
    <p>No se encontraron usuarios con mediciones.</p>
<?php endif; ?>

<!-- Scripts de Bootstrap y jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Enlazar el archivo JavaScript personalizado -->
<script src="/frontend/js/pagina_admin.js"></script>

</body>
</html>
