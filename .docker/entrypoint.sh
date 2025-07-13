#!/bin/bash

echo "Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "Running Laravel artisan commands..."
php artisan storage:link || true
php artisan config:cache || true

echo "Starting PHP-FPM..."
exec php-fpm
