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

# Instalar Xdebug 2.9.8 (compatible con PHP 7.1)
RUN pecl install xdebug-2.9.8 && docker-php-ext-enable xdebug

# Configurar Xdebug en php.ini
RUN echo "zend_extension=xdebug.so" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.coverage_enable=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/php.ini

# Instalar Composer (compatible con PHP 7.1)
COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copiar solo los archivos necesarios para instalar dependencias
COPY composer.json composer.lock ./

# Instalar las dependencias con Composer

# Copiar el resto de los archivos de la aplicación
COPY . .

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto
EXPOSE 8000

# Comando para iniciar el servidor PHP embebido
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
