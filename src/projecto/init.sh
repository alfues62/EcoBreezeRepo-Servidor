#!/bin/bash

# Copia la configuración de Apache
#!/bin/bash

# Copia la configuración de Apache
cat <<EOL > /etc/apache2/sites-available/000-default.conf
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html

    # Configuración para la API    
    Alias /api /var/www/api        

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /var/www/api>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOL

# Cambia permisos y propietarios
chmod -R 755 /var/www/api
chown -R www-data:www-data /var/www/api

# Cambia permisos y propietarios para /var/www/html
chmod -R 755 /var/www/html
chown -R www-data:www-data /var/www/html

# Reinicia Apache
service apache2 restart

# Mantener el contenedor en ejecución
tail -f /dev/null
