FROM php:8.3-apache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=node:22-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
 && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

RUN set -eux; \
    apt-get update && apt-get install -y libicu-dev libzip-dev git unzip && rm -rf /var/lib/apt/lists/*; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql mysqli intl zip; \
    a2enmod rewrite; \
    git config --global --add safe.directory /var/www/html
