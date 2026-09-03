# Use the official PHP image with Apache
# NOTE: If your project requires PHP 8.3, change 8.2 to 8.3 below
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

# Set the working directory
WORKDIR /var/www/html

# Copy your project files into the container
COPY . .

# Set environment variable to allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install PHP dependencies
# --ignore-platform-reqs: Fixes "requires ext-xyz" or PHP version errors in Docker
# --no-interaction: Prevents Composer from hanging waiting for user input
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs --no-interaction

# For Laravel/Symfony: Set document root to public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Enable Apache mod_rewrite (required for Laravel/Symfony routing)
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80