#!/bin/bash
set -e

LISTEN_PORT=${PORT:-8080}

# Update Apache to listen on Railway's PORT
sed -i "s/Listen 80/Listen $LISTEN_PORT/g" /etc/apache2/ports.conf
sed -i "s/*:80>/*:$LISTEN_PORT>/g" /etc/apache2/sites-available/000-default.conf

# Laravel setup
php artisan migrate --force
php artisan storage:link

# Start Apache
exec apache2-foreground
