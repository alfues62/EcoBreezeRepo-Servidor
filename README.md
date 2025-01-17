# EcoBreezeRepo-Servidor
En este repositorio se almacenan todos los codigos relacionados con la página web de nuestro proyecto.

## Guía de uso
Para poder usar este repositorio deberás seguir estos pasos:

    1. Descargar este repositorio, descomprimir el .zip y posicionarse en la carpeta src
    
    2. En una terminal ejecutar los siguientes comandos:
        docker login
        docker-compose up --build -d
    
    Una vez ejecutados estos comandos se habrá creado y activado un container de docker llamado "ecobreeze" que tiene 2 componentes: "php-backebd"y "mysql-db", y una vez activo podemos acceder a la página.

    3. En cualquier visualizador de bases de datos SQL, por ejemplo MySQLWorkbench, crear una nueva conexion. Para evitar problemas en un futuro utilizar un puerto diferente al 3306, ya que MySQL usa este por defecto. El nombre de la conexión y base de datos deberá ser EcoBreeze. Para mayor comodidad dejaremos el hostname como localhost. 

    4. Una vez hecha la conexion ya podremos visualizar la base de datos.

    5. En un navegador, por ejemplo Chrome, teclearemos la siguiente dirección:
        - localhost:8080
    
    Así ya podremos visitar nuestra página web, si tenemos las partes de Arduino y Android activadas podremos ver los datos que enviamos desde Android.

## Estructura del proyecto:

    Este repositorio se divide en 3 carpetas: doc, src y test.

        1. doc: Contiene toda la documentación de esta parte del proyecto.

        2. src: Carpeta principal que contiene todo el codigo fuente del repositorio.

            2.1. backend: Esta carpeta contiene todas las secciones relacionadas con el funcionamiento lógico de la parte del servidor.

                2.1.1. api: Contiene los archivos responsables de realizar las acciones que querramos con los datos recibidos del CRUD. También contiene el archivo .yaml que documenta la api.

                2.1.2. controllers: Archivos CRUD que actuan como intermediarios (lógica de negocio) entre la base de datos y la api, permitiendo a esta cambiar o consultar partes de la base de datos.

                2.1.3. login: Contiene los archivos php responsables de realizar el login.

                2.1.4. mediciones_api: Contiene los archivos necesarios para obtener las medidas de las estaciones de medida oficiales.

                2.1.5. pagina_admin: Contiene todos los archivos necesarios para la pagina de administradores.

                2.1.6. pagina_usuarios: Contiene los archivos necesarios para la pagina de usuarios.

                2.1.7. recuperar_contraseña: Contiene los archivos necesarios para la logica de la recuperación de contraseñas.

                2.1.8. registrar: Contiene los archivos de la página de registros, como también la lógica de esta.

                2.1.9. db: Contiene el init.sql responsable de crear la base de datos.

            2.2. frontend: Esta carpeta contiene todos los recursos de la página web responsables de la visualización de esta.

                2.2.1. css: Contiene los archivos css de la página web.

                2.2.2. img: Contiene los recursos de imagenes de la web.

                2.2.3. js: Contiene los archivos JavaScript para funcionalidades de la web.

                2.2.4. php: Contiene los archivos php responsables de la estructura de cada pagina.

            2.3. logs: Contiene el archivo app.log responsable de anotar los errores que podamos encontrar al estar progrmamndo.
            
        3. test: Contiene todos los tests realizados en este repositorios.

### Versión 1.0