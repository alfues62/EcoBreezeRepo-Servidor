<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>EcoBrezze</title>
    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Core theme CSS -->
    <link rel="stylesheet" href="/frontend/css/index.css">
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/inicio_usuario.css">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Poppings -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
<!-- Leaflet (Mapa y mapa de calor) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.heat/dist/leaflet-heat.css">
    <!-- Incluir Leaflet y plugin de mapa de calor -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

    
    <!-- Scripts de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    

<!-- Navigation -->
<nav class="navbar fixed-top" style="margin-bottom: 200px" >
    <div class="navbar-brand">
        <a href="/frontend/index.php">
            <img src="/frontend/img/logoBio.png" alt="Logo" class="logo">
        </a>
        <span>EcoBreeze</span>
    </div>
    <ul class="navbar-nav">
        <li><a href="/backend/pagina_usuario/main_usuario.php">INICIO</a></li>
        <li><a href="/backend/pagina_usuario/mapa_usuarios.php">MAPA GLOBAL</a></li>
        <li><a href="/backend/logout.php">CERRAR SESIÓN</a></li>
        <a href="../../php/edicion_usuario.vista.php">
            <img src="/frontend/img/perfil.png" alt="Logo" class="logo">
        </a>
        
    </ul>
</nav>

<div style="margin-top :50px ;width: 100%; height: 80vh;">
    <div id="map" style="width: 100%; height: 100%;"></div>
</div>
    

    <!-- Mensajes de éxito y error -->
    <div id="successMessage" style="display:none;">
            <?php echo isset($success_message) && $success_message != '' ? $success_message : ''; ?>
        </div>
        <div id="errorMessage" style="display:none;">
            <?php echo isset($error_message) && $error_message != '' ? $error_message : ''; ?>
        </div>

    <!-- Modal de Éxito -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Éxito</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
                <div class="modal-footer">
                    <button onclick="redirectToSamePage()" type="button" class="btn btn-success" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Error -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

<!-- Scripts de Bootstrap y jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-background-lines"></script>
<!-- Enlazar el archivo JavaScript personalizado -->
<script>
    // Pasa las mediciones de PHP a JavaScript
    window.mediciones = <?php echo $mediciones_json; ?>;
    window.mapaMediciones = <?php echo $mediciones_json; ?>;
</script>
<script src="/frontend/js/mapa.js"></script>
<script src="/frontend/js/pagina_usuario.js"></script>


</body>
</html>