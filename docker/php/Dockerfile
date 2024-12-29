FROM php:8.3-fpm-alpine

# Install PostgreSQL dependencies
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install additional utilities
RUN apk add --no-cache \
    zip \
    unzip \
    git \
    mc \
    dcron

RUN docker-php-ext-install opcache

# Create directories for logs and set permissions
RUN mkdir -p /var/log/php-fpm && \
    touch /var/log/php-fpm/error.log && \
    touch /var/log/php-fpm/access.log && \
    chown -R www-data:www-data /var/log/php-fpm && \
    chmod -R 755 /var/log/php-fpm

# Copy configuration files
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/crontab /etc/crontabs/root

# Copy and set permissions for entrypoint script
COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set working directory
WORKDIR /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

ENTRYPOINT ["docker-entrypoint.sh"]