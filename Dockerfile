# Usa una imagen base oficial de PHP 7.1
FROM php:7.1-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libmcrypt-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mcrypt \
    mbstring \
    tokenizer \
    zip

# Instalar Composer (compatible con PHP 7.1)
COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos de la aplicación
COPY . .

# Instalar las dependencias con Composer
RUN composer install
RUN php artisan jwt:secret

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html

# Puerto por defecto
EXPOSE 8000

# Comando para iniciar el servidor PHP embebido
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
