# ==============================================================================
# SHILPA LMS — Enterprise Production Multi-Stage Dockerfile
# ==============================================================================

# ------------------------------------------------------------------------------
# Stage 1: Build Application Dependencies
# ------------------------------------------------------------------------------
FROM php:8.2-fpm-alpine AS builder

WORKDIR /var/www/html

# Install build dependencies
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Install PHP extensions required for building and running
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        bcmath \
        intl \
        opcache \
        zip \
        gd

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy application manifest files
COPY composer.json composer.lock* ./

# Install optimized production dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# ------------------------------------------------------------------------------
# Stage 2: Production Runtime
# ------------------------------------------------------------------------------
FROM php:8.2-fpm-alpine

LABEL maintainer="SHILPA LMS Engineering <engineering@shilpalms.com>"
LABEL version="2.0.0"

WORKDIR /var/www/html

# Install runtime libraries
RUN apk add --no-cache \
    icu-libs \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    bash

# Copy compiled PHP extensions from builder
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Configure production PHP and OPcache
COPY docker/php.ini /usr/local/etc/php/conf.d/99-production.ini

# Copy vendor dependencies from builder
COPY --from=builder /var/www/html/vendor /var/www/html/vendor

# Copy application source code
COPY . /var/www/html

# Prepare storage directories and set non-root ownership
RUN mkdir -p storage/app/public \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true \
    && chmod -R 775 /var/www/html/storage 2>/dev/null || true \
    && chmod +x /var/www/html/docker/entrypoint.sh /var/www/html/bin/*

EXPOSE 9000

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
CMD ["php-fpm"]
