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
    unzip \
    git \
    gnupg \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip mbstring tokenizer

RUN echo "zend_extension=xdebug.so" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/php.ini \
    && echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/php.ini
COPY --from=composer:1 /usr/bin/composer /usr/bin/composer


RUN curl -sSLo /tmp/sonar-scanner-cli.zip https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.6.2.2472-linux.zip \
    && unzip /tmp/sonar-scanner-cli.zip -d /opt/ \
    && mv /opt/sonar-scanner-4.6.2.2472-linux /opt/sonar-scanner \
    && ln -s /opt/sonar-scanner/bin/sonar-scanner /usr/local/bin/sonar-scanner \
    && rm /tmp/sonar-scanner-cli.zip

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar composer.json y composer.lock primero (cache eficiente)
COPY composer.json composer.lock ./

# Instalar dependencias (Composer 1)
RUN composer install --no-interaction --prefer-dist || true

# Copiar el resto de la aplicación
COPY . .

# Dar permisos a www-data
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto (para servidor embebido)
EXPOSE 8000

# Comando por defecto: servidor embebido de PHP
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
