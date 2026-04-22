FROM php:8.3-apache

# =========================
# Working directory
# =========================
WORKDIR /var/www/html

# =========================
# System dependencies + PHP extensions
# =========================
RUN apt-get update && apt-get install -y \
    git curl unzip zip \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath

# =========================
# Enable Apache rewrite
# =========================
RUN a2enmod rewrite

# =========================
# Copy project
# =========================
COPY . .

# =========================
# Composer install
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-scripts

# =========================
# Laravel permissions
# =========================
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data /var/www/html

# =========================
# Apache config (point to /public)
# =========================
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# =========================
# Laravel optimization + migration
# =========================
RUN php artisan config:clear
RUN php artisan cache:clear

# ⚠️ safe migration (optional)
# RUN php artisan migrate --force

# =========================
# Start server
# =========================
CMD php artisan migrate --force && apache2-foreground