FROM php:8.2-apache

# Install system dependencies and common PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

# Force install, ignoring all platform requirements, scripts, and plugins
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN a2enmod rewrite

EXPOSE 80