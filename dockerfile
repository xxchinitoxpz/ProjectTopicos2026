FROM php:8.3-apache

# Dependencias del sistema (incluye curl, necesario para instalar Node.js)
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libzip-dev \
    curl

# Instalar Node.js 20 (necesario para compilar los assets de Laravel con Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Limpiar cache de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite de Apache (rutas amigables de Laravel)
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar el proyecto completo
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Instalar dependencias de Node y compilar assets (genera public/build/manifest.json)
RUN npm install && npm run build

# Permisos correctos para Laravel (solo carpetas que Laravel necesita escribir)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80