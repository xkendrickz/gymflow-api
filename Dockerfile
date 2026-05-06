FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions including pgsql
RUN docker-php-ext-install \
    mbstring \
    zip \
    pdo \
    pdo_pgsql \
    pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy Apache config
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start script
COPY start.sh /start.sh
RUN chmod +x /start.sh
CMD ["/start.sh"]