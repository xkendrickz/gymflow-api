#!/bin/bash

# Run migrations
php /var/www/artisan migrate --force
php /var/www/artisan db:seed --force

# Cache config
php /var/www/artisan config:cache
php /var/www/artisan route:cache

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"