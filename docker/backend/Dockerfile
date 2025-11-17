FROM php:8.2-fpm-bullseye

WORKDIR /var/www/html

# --- Debian ミラー修正（HTTP が落ちてるので HTTPS へ変更） ---
RUN sed -i 's|http://deb.debian.org/debian|https://deb.debian.org/debian|g' /etc/apt/sources.list \
    && sed -i 's|http://security.debian.org/debian-security|https://security.debian.org/debian-security|g' /etc/apt/sources.list

# パッケージのインストール
RUN apt-get update && \
    apt-get install -y zip unzip curl git && \
    docker-php-ext-install pdo pdo_mysql

# Composer をコピー
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laravel をコピー
COPY ./backend /var/www/html

# vendor インストール
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 権限
RUN chown -R www-data:www-data storage bootstrap/cache

CMD ["php-fpm"]
