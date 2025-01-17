<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios y Mediciones</title>
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/pagina_admin.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Leaflet (Mapa y mapa de calor) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.heat/dist/leaflet-heat.css">
    <!-- Incluir Leaflet y plugin de mapa de calor -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-heat@0.2.0/leaflet-heat.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
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

<?php if (!empty($success_message)): ?>
    <p class="success" id="successMessage" style="display:none;"><?php echo $success_message; ?></p>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <p class="error" id="errorMessage" style="display:none;"><?php echo $error_message; ?></p>
<?php endif; ?>

<?php if (!empty($usuarios)): ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID <i class="bi bi-caret-up-fill cursor-pointer"></i> <i class="bi bi-caret-down-fill cursor-pointer"></i></th>
                <th>Nombre <i class="bi bi-caret-up-fill cursor-pointer"></i> <i class="bi bi-caret-down-fill cursor-pointer"></i></th>
                <th>Apellidos <i class="bi bi-caret-up-fill cursor-pointer"></i> <i class="bi bi-caret-down-fill cursor-pointer"></i></th>
                <th>Email <i class="bi bi-caret-up-fill cursor-pointer"></i> <i class="bi bi-caret-down-fill cursor-pointer"></i></th>
                <th>Última Medición <i class="bi bi-caret-up-fill cursor-pointer"></i> <i class="bi bi-caret-down-fill cursor-pointer"></i></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr class="usuarioRow">
                    <td><?php echo htmlspecialchars($usuario['ID']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['Nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['Apellidos']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['Email']); ?></td>
                    <td><?php echo $usuario['UltimaMedicion'] === 'N/A' ? "<span class='na'>N/A</span>" : htmlspecialchars($usuario['UltimaMedicion']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <nav>
        <ul class="pagination" id="pagination"></ul>
    </nav>
<?php else: ?>
    <p>No se encontraron usuarios con mediciones.</p>
<?php endif; ?>

<div id="map" style="height: 400px;"></div>

<!-- Formulario oculto para enviar solicitudes de eliminación -->
<form id="deleteForm" method="POST" action="/backend/pagina_admin/main_admin.php">
    <input type="hidden" name="action" value="eliminar_usuario">
    <input type="hidden" name="id" id="deleteUserId">
</form>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" role="dialog" aria-labelledby="confirmarEliminarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="modalUsuarioDatos"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de resultado -->
<div class="modal fade" id="resultadoModal" tabindex="-1" role="dialog" aria-labelledby="resultadoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultadoModalLabel">Resultado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="resultadoModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var mapaMediciones = <?php echo json_encode($datosMapa); ?>;

</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/frontend/js/pagina_admin.js"></script>
<script src="/frontend/js/mapa_admin.js"></script>

</body>
</html>
