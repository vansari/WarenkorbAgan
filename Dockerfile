ARG PHP_VERSION=8.2
FROM alpine:latest as base

RUN mkdir -p "/usr/local/etc/php/conf.d/" \
    && printf 'zend_extension=xdebug\n\n[xdebug]\nxdebug.mode=develop,debug\nxdebug.client_host=host.docker.internal\nxdebug.start_with_request=yes\n' > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "error_reporting=E_ALL" > /usr/local/etc/php/conf.d/error_reporting.ini

FROM php:${PHP_VERSION}-fpm as fpm
COPY --from=base /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /code

FROM php:${PHP_VERSION}-cli as cli
COPY --from=base /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y unzip zip && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /code