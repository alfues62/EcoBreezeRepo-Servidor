<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios y mediciones </title>

    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Core theme CSS -->
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/pagina_admin.css">
    

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Poppings -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

<body>


<!-- Navigation -->
<nav class="navbar fixed-top">
    <div class="navbar-brand">
        <a href="#seccionInicio">
            <img src="/frontend/img/logoBio.png" alt="Logo" class="logo">
        </a>
        <span>EcoBreeze</span>
    </div>
    <ul class="navbar-nav">
        <li><a href="/frontend/index.php" class="btn btn-volver">SALIR</a></li>
        
    </ul>
</nav>


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
</body>

<!-- Scripts de Bootstrap y jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Enlazar el archivo JavaScript personalizado -->
<script src="/frontend/js/pagina_admin.js"></script>

</html>
