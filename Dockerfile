FROM debian:buster
FROM php:8-apache

RUN apt-get update \
    && apt-get install -f \
    && apt-get install -y git \
    && apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Instalar composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar los archivos de la aplicación al contenedor
RUN git config --global --add safe.directory /var/www/html
RUN git clone https://github.com/Josafast/LoginAPIphp.git /var/www/html/ && git checkout 49f6222

# Instalar dependencias de la aplicación mediante composer
RUN echo '{"require": {"firebase/php-jwt": "^5.3", "slim/slim": "^3.12"}}' > /var/www/html/composer.json \
    && cd /var/www/html/ \
    && composer install

# Exponer el puerto 80 para el servidor web
EXPOSE 80