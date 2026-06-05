# syntax=docker/dockerfile:1

# ----------------------------------------------------------------------------
# Stage 1: Compile Tailwind CSS
# Scans the PHP views and JS for class names and outputs a single minified
# stylesheet. No Node.js is present in the final runtime image.
# ----------------------------------------------------------------------------
FROM node:20-alpine AS assets
WORKDIR /build
COPY package.json ./
RUN npm install
COPY tailwind.config.js postcss.config.js ./
COPY public/css/src ./public/css/src
COPY app ./app
COPY public/js ./public/js
COPY index.php ./index.php
RUN npm run build \
    && mkdir -p public/vendor/fullcalendar \
    && cp node_modules/fullcalendar/index.global.min.js public/vendor/fullcalendar/index.global.min.js

# ----------------------------------------------------------------------------
# Stage 2: Install PHP (Composer) dependencies
# ----------------------------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-scripts \
        --prefer-dist \
        --optimize-autoloader

# ----------------------------------------------------------------------------
# Stage 3: Runtime — PHP 8.3 + Apache
# ----------------------------------------------------------------------------
FROM php:8.3-apache AS app

# System packages + PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends curl \
    && docker-php-ext-install pdo_mysql mysqli \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Apache virtual host (enables .htaccess via AllowOverride All)
COPY docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Application source
COPY . /var/www/html

# Composer dependencies from stage 2
COPY --from=vendor /app/vendor /var/www/html/vendor

# Compiled Tailwind stylesheet from stage 1
COPY --from=assets /build/public/css/app.css /var/www/html/public/css/app.css

# Vendored FullCalendar bundle from stage 1 (offline, no runtime CDN)
COPY --from=assets /build/public/vendor/fullcalendar /var/www/html/public/vendor/fullcalendar

# Entrypoint: generates .env from environment, then starts Apache
COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && chown -R www-data:www-data /var/www/html

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
