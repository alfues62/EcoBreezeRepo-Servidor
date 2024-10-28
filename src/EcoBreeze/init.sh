#!/bin/bash

# Copia la configuración de Apache
cat <<EOL > /etc/apache2/sites-available/000-default.conf
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/backend

    ServerName ecobreeze

    # Configuración para la API    
    Alias /api /var/www/html/backend/api       

    <Directory /var/www/html/backend/api>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /var/www/html/backend>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOL

chmod -R 755 /var/www/html/backend/api
chown -R www-data:www-data /var/www/html/backend/api

chmod -R 755 /var/www/backend
chown -R www-data:www-data /var/www/backend

chmod 666 /var/www/html/logs/app.log
chown www-data:www-data /var/www/html/logs/app.log

a2enmod rewrite

if apachectl configtest; then
    echo "Configuración de Apache válida."
else
    echo "Error en la configuración de Apache."
    exit 1
fi

service apache2 restart

tail -f /dev/null
