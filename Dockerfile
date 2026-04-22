FROM php:8.3-apache

WORKDIR /var/www/html

# =========================
# SYSTEM DEPENDENCIES (IMPORTANT)
# =========================
RUN apt-get update && apt-get install -y \
    git curl unzip zip \
    libzip-dev \
    libpq-dev \
    postgresql-client \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# =========================
# PHP EXTENSIONS (CLEAN VERSION)
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
# APACHE
# =========================
RUN a2enmod rewrite

# =========================
# PROJECT
# =========================
COPY . .

# =========================
# COMPOSER
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-scripts

# =========================
# PERMISSIONS
# =========================
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data /var/www/html

# =========================
# LARAVEL PUBLIC
# =========================
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# =========================
# START
# =========================
CMD apache2-foreground