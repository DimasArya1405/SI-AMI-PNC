FROM php:8.2-cli

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd xml zip dom \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy semua file project
COPY . .

# Install PHP dependencies (tanpa dev, sudah ada public/build dari git)
RUN composer install --no-dev --optimize-autoloader

# Set permissions untuk storage & cache
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# Jalankan migration & server saat container start
CMD php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
