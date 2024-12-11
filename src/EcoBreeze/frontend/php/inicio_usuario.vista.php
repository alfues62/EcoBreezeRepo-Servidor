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

</head>
<body>
    

<!-- Navigation -->
<nav class="navbar fixed-top">
    <div class="navbar-brand">
        <a href="/frontend/index.php">
            <img src="/frontend/img/logoBio.png" alt="Logo" class="logo">
        </a>
        <span>EcoBreeze</span>
    </div>
    <ul class="navbar-nav">
        <li><a href="/backend/pagina_usuario/main_usuario.php">INICIO</a></li>
        <li><a href="#seccionProducto">MAPA GLOBAL</a></li>
        <li><a href="/backend/logout.php">CERRAR SESIÓN</a></li>
        <a href="../../php/edicion_usuario.vista.php">
            <img src="/frontend/img/perfil.png" alt="Logo" class="logo">
        </a>
        
    </ul>
</nav>


<!-- Inicio Section -->
<section id="inicio" class="text-center">
    <div class="container-inicio">
        <!-- Plan Gratuito -->
        <div class="inicio-container1">
            <div class="plan">
                <h1 class="section-title">DISPOSITIVO ECOBREZZE</h1>
                <img src="/frontend/img/producti.png" alt="Imagen del Producto Eco Breeze, un dispositivo para monitoreo de calidad del aire" class="product-image">

            </div>
        </div>

        <!-- Línea de separación -->
        <div class="separador separador-inicio"></div>

        <!-- Plan Premium -->
        <div class="inicio-container2">
            <div class="plan_inicio">
                <h2 h2-title>¡ESTÁS A SOLO UN PASO DE HACERTE PREMIUM!</h2>
                <ul>
                    <h2>Plan <span style="color: #4ca5aa;">PREMIUM  0,99 €</span></h2>
                <ul-textos>
                    <li>Dispositivo EcoBreeze</li>
                    <li>Acceso App Móvil</li>
                    <li>Registro de Datos Completo</li>
                    <li>Mediciones en Tiempo Real</li>
                </ul>
                <button class="btn btn-comprar" type="button">COMPRAR AHORA</button>
            </div>
        </div>
    </div>
</section>