#!/bin/bash

# Run migrations
php artisan migrate --force

# Cache config for production
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
apache2-foreground