<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>EcoBrezze</title>
    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Core theme CSS -->
    <link rel="stylesheet" href="/frontend/css/index.css">
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/inicio_usuario.css">
    <link rel="stylesheet" href="/frontend/css/pagina_usuario.css">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Poppings -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>
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
        <li><a href="#seccionInicio">INICIO</a></li>
        <li><a href="#seccionProducto">GRÁFICA</a></li>
        <li><a href="/backend/logout.php">CERRAR SESIÓN</a></li>
        <a href="#seccionInicio">
            <img src="/frontend/img/perfil.png" alt="Logo" class="logo">
        </a>        
    </ul>
</nav>

    <?php if ($usuario): ?>
        
        <div class="container mt-5">
            <h2>Gráfica de Mediciones</h2>
            <div style="width: 100%; max-width: 800px; margin: 0 auto;">
                <canvas id="graficaMediciones" width="400" height="200"></canvas>
            </div>
            
            <div id="error-message" style="color: red; font-weight: bold;"></div>
            <div id="mediciones-container"></div>  <!-- Contenedor para mostrar las mediciones recibidas -->
        </div>
    <?php endif; ?>

    
<!-- Scripts de Bootstrap y jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Enlazar el archivo JavaScript personalizado -->
<script>
    // Pasa las mediciones de PHP a JavaScript
    window.mediciones = <?php echo $mediciones_json; ?>;
</script>
<script src="/frontend/js/pagina_usuario.js"></script>
</body>
</html>
