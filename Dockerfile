# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies and common PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy your project files into the container
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 80 (Render automatically routes traffic to port 80)
EXPOSE 80