FROM php:8.3-apache

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip git curl libzip-dev zip libonig-dev libxml2-dev \
    && docker-php-ext-install zip pdo pdo_pgsql mbstring exif pcntl bcmath

# Copy project
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies (NO SCRIPTS to avoid errors)
RUN composer install --ignore-platform-reqs --no-dev --optimize-autoloader --no-scripts

# Apache config (point to public/)
RUN a2enmod rewrite
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Permissions
RUN chown -R www-data:www-data /var/www/html

CMD apache2-foreground