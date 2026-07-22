FROM php:8.3-fpm-alpine

# Install system dependencies, PHP extensions, Node.js, and npm
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_mysql bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# 1. Install PHP dependencies first (This fixes the missing Flux CSS issue)
RUN composer install --no-dev --optimize-autoloader

# 2. Build frontend assets now that vendor/ directory exists
RUN npm install && npm run build

# Set permissions for Laravel storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# Run optimization and start the server environment
CMD php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan storage:link && nginx -g "daemon off;"
