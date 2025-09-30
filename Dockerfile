FROM php:7.1-fpm

RUN apt-get update && apt-get install -y \
    libmcrypt-dev libzip-dev zip unzip curl gnupg \
    && docker-php-ext-install pdo pdo_mysql mbstring tokenizer zip

# mcrypt mejor por PECL para evitar errores futuros
RUN pecl install mcrypt-1.0.1 && docker-php-ext-enable mcrypt

RUN pecl install xdebug-2.9.8 && docker-php-ext-enable xdebug

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

WORKDIR /var/www/html

COPY composer.json composer.lock ./
COPY . .

RUN chown -R www-data:www-data /var/www/html

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
