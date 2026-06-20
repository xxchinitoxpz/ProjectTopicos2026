FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libzip-dev

RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80