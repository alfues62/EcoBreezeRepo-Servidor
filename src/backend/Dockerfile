# Usar una imagen oficial de PHP con Apache
FROM php:8.1-apache

# Instalar extensiones necesarias para conectar PHP con MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copiar los archivos de PHP al directorio del servidor Apache
COPY src/ /var/www/html/

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite
