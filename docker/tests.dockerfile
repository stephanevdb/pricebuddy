ARG PHP_VERSION=8.4
ARG NODE_VERSION=20

FROM node:${NODE_VERSION}-bookworm-slim AS node

FROM php:${PHP_VERSION}-apache-bookworm

ENV NODE_VERSION=${NODE_VERSION}
ENV EDITOR=nano
WORKDIR /app

RUN apt update && apt install -y \
        cron \
        supervisor \
        nano \
        zip \
        unzip \
        libzip-dev \
        zlib1g-dev \
        libicu-dev \
        netcat-traditional \
        default-mysql-client \
        less \
        && apt-get clean

RUN docker-php-ext-install exif \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install pcntl posix \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-install zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

# Install node
COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin
RUN node -v && npm -v

