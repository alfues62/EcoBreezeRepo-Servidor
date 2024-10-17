# EcoBreezeRepo-Servidor
En este repositorio se almacenan todos los codigos relacionados con la página web de nuestro proyecto.

## Guía de uso
Para poder usar este repositorio deberás seguir estos pasos:

    1. Descargar este repositorio, descomprimir el .zip y posicionarse en la carpeta src
    
    2. En una terminal ejecutar los siguientes comandos:
        docker login
        docker pull haoxux/biometria-php:latest
        docker pull haoxux/biometria-mysql:latest
        docker-compose up --build (este comando debe ser ejecutado al nivel de la carpeta "src")
    
    Una vez ejecutados estos comandos se habrá creado y activado un container de docker llamado "biometria", y una vez activo podemos acceder a la página.

    3. En cualquier visualizador de bases de datos SQL, por ejemplo MySQLWorkbench, crear una nueva conexion en la que se usen los siguientes parametros:
        - Hostname: localhost
        - Puerto: 3306
        - Username: root
        - Password: 123456

    4. Una vez hecha la conexion, ejecutar el codigo: 

        CREATE DATABASE IF NOT EXISTS DockerBBDD;
        SET GLOBAL time_zone = 'Europe/Madrid';
        SET time_zone = 'Europe/Madrid';
        USE DockerBBDD;

        CREATE TABLE acciones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numero INT NOT NULL,
            fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        SELECT * FROM acciones;

    5. En un navegador, por ejemplo Chrome, teclearemos la siguiente dirección:
        - localhost:8080
    
    Así ya podremos visitar nuestra página web, si tenemos las partes de Arduino y Android activadas podremos ver los datos que enviamos desde Android.

## Diseño BBDD

MySQL

    CREATE DATABASE IF NOT EXISTS DockerBBDD;
    SET GLOBAL time_zone = 'Europe/Madrid';
    SET time_zone = 'Europe/Madrid';
    USE DockerBBDD;

    CREATE TABLE acciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        numero INT NOT NULL,
        fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    SELECT * FROM acciones;