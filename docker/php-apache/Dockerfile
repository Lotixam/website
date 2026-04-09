FROM php:8.3-apache

RUN set -eux; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql mysqli; \
    a2enmod rewrite
