FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
        bash \
        curl \
        libxml2-dev \
        libzip-dev \
        oniguruma-dev \
        postgresql-dev \
        zip \
        unzip \
    && docker-php-ext-install pdo_pgsql pgsql mbstring xml zip bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
