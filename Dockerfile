# Imagen base oficial de PHP 7.2 con FPM
FROM php:7.2-fpm

# Fix repos antiguos de Debian (PHP 7.2 está basado en stretch/buster)
RUN sed -i 's|deb.debian.org|archive.debian.org|g' /etc/apt/sources.list \
    && sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list \
    && sed -i '/debian-security/d' /etc/apt/sources.list

# Instalar extensiones necesarias para Lumen 5.8
RUN apt-get update && apt-get install -y \
    libmcrypt-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    unzip \
    git \
    gnupg \
    curl \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install pdo pdo_mysql zip mbstring tokenizer gd

COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar composer.json y composer.lock primero (cache eficiente)
COPY composer.json composer.lock ./

# Instalar solo dependencias de runtime (sin dev/ phpunit)
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader || true

# Copiar el resto de la aplicación
COPY . .

# Dar permisos a www-data
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto (para servidor embebido)
EXPOSE 8000

# Comando por defecto: servidor embebido de PHP con front controller
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public", "public/index.php"]
