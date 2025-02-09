FROM php:8.2-fpm-alpine3.18

WORKDIR /var/www/html

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    supervisor \
    nodejs \
    npm \
    mariadb-client \
    nginx \
    icu-dev \
    libintl


RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    gd \
    xml \
    intl \
    opcache \
    zip


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

COPY docker/nginx.conf /etc/nginx/nginx.conf

# Set permissions for storage and bootstrap cache
RUN mkdir -p /var/www/html/storage/framework/cache/data && \
    mkdir -p /var/www/html/storage/framework/sessions && \
    mkdir -p /var/www/html/storage/framework/views && \
    chmod -R 775 /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html/storage

# Create necessary log directories
RUN mkdir -p /var/log/supervisor /var/log/nginx /var/log/php-fpm

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

RUN php artisan config:clear && \
    php artisan cache:clear && \
    php artisan view:clear && \
    php artisan route:clear && \
    php artisan filament:optimize-clear

RUN php artisan filament:optimize

# Configure PHP-FPM and Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/php.ini

EXPOSE 80

# Use Supervisor to manage processes
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
