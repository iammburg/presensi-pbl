# Stage 1: deps & build
FROM php:8.2-fpm AS builder

# install extensions
RUN apt-get update && apt-get install -y \
    git zip unzip libzip-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev \
 && docker-php-ext-install zip pdo pdo_mysql mbstring exif pcntl bcmath gd

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# copy rest & cache
COPY . .
RUN composer dump-autoload --optimize \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Stage 2: runtime
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y libzip-dev libonig-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev \
 && docker-php-ext-install zip pdo pdo_mysql mbstring exif pcntl bcmath gd
WORKDIR /var/www
COPY --from=builder /var/www /var/www

# permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
