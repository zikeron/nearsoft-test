FROM php:7.2-fpm

COPY ./app /var/www/nearsoft

RUN apt-get update \
    && apt-get install git zip unzip zlib1g-dev libzip-dev libicu-dev -y \
    && docker-php-ext-install zip  \
    && docker-php-ext-configure zip \
    && docker-php-ext-install intl

RUN yes | pecl install xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/nearsoft

COPY xdebug.ini $PHP_INI_DIR/conf.d/20-xdebug.ini
COPY ./php.ini $PHP_INI_DIR/conf.d/

CMD ["php-fpm"]