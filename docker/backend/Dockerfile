FROM php:8.2-fpm

WORKDIR /var/www/html

COPY ./backend .

RUN apt-get update
RUN apt-get install -y \
    zip unzip curl \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install

