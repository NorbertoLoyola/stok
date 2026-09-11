FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --ignore-platform-req=ext-intl

FROM node:20 AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY . .
RUN npm run build

FROM php:8.2-apache AS ospos
LABEL maintainer="jekkos"

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libgd-dev \
    && docker-php-ext-install mysqli bcmath intl gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite

RUN echo "date.timezone = \"\${PHP_TIMEZONE}\"" > /usr/local/etc/php/conf.d/timezone.ini

WORKDIR /app
COPY --chown=www-data:www-data . /app
COPY --chown=www-data:www-data --from=vendor /app/vendor /app/vendor
COPY --chown=www-data:www-data --from=assets /app/public /app/public
RUN chmod 750 /app/writable/logs /app/writable/uploads /app/writable/cache /app/public/uploads /app/public/uploads/item_pics \
    && chmod 640 /app/writable/uploads/importCustomers.csv \
    && ln -s /app/*[^public] /var/www \
    && rm -rf /var/www/html \
    && ln -nsf /app/public /var/www/html
