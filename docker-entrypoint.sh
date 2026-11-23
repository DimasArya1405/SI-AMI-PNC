#!/bin/bash
set -e

LISTEN_PORT=${PORT:-8080}

# Tulis ulang Apache config dari scratch (hindari masalah sed)
cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:${LISTEN_PORT}>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
        Options -Indexes
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Update ports.conf
echo "Listen ${LISTEN_PORT}" > /etc/apache2/ports.conf

# Laravel setup
php artisan migrate --force
php artisan storage:link

# Start Apache
exec apache2-foreground
