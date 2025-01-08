FROM php:8.3-fpm-alpine3.19

WORKDIR "/var/www/organization"

ARG WWWGROUP
ARG WWWUSER

USER root

RUN apk update && apk add --no-cache \
    tzdata \
    autoconf \
    linux-headers \
    gcc \
    g++ \
    git \
    make \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    zip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    jpeg-dev \
    librdkafka-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg

RUN docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        zip \
        exif \
        intl \
        gd \
        pcntl \
#    && pecl install xdebug-3.3.0\
    && pecl update-channels \
    && pecl install -o -f redis rdkafka \
    && docker-php-ext-enable \
#        xdebug \
        redis \
        rdkafka\
        pcntl \
    && rm -rf /tmp/pear

RUN addgroup -g $WWWGROUP www && \
    adduser -u $WWWUSER -G www -s /bin/sh -D www

USER www
#RUN composer install
