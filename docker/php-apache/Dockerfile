FROM php:8.3-apache

RUN set -eux; \
    apt-get update && apt-get install -y libicu-dev && rm -rf /var/lib/apt/lists/*; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql mysqli intl; \
    a2enmod rewrite
