FROM php:8.3-apache

WORKDIR /var/www/html

# =========================
# Install system dependencies FIRST
# =========================
RUN apt-get update && apt-get install -y \
    git curl unzip zip \
    libzip-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# =========================
# Install PHP extensions
# =========================
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    mbstring \
    bcmath \
    exif \
    pcntl

# =========================
# Apache config
# =========================
RUN a2enmod rewrite

# =========================
# Copy app
# =========================
COPY . .

# =========================
# Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-scripts

# =========================
# Permissions
# =========================
RUN chmod -R 775 storage bootstrap/cache

RUN chown -R www-data:www-data /var/www/html

# =========================
# Point to Laravel public
# =========================
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# =========================
# Start
# =========================
CMD apache2-foreground