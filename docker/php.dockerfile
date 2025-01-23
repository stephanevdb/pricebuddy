ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-apache-bookworm

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
        && apt-get clean

RUN docker-php-ext-install exif \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install pcntl posix \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-install zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

#    # Enable pcntl function required by horizon.
#    && sed -i 's/pcntl_signal,//g' /usr/local/etc/php/php.ini \
#    && sed -i 's/pcntl_async_signals,//g' /usr/local/etc/php/php.ini \
#    && sed -i 's/pcntl_alarm,//g' /usr/local/etc/php/php.ini

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

COPY ../.. /app
COPY ../../docker/php/php.ini /usr/local/etc/php/conf.d/zzz-php-overrides.ini
COPY ../../docker/php/schedule-cron /etc/cron.d/schedule-cron
COPY ../../docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ../../docker/php/entrypoint.sh /app-entrypoint.sh
COPY ../../docker/php/start-cron.sh /start-cron.sh

RUN rm -rf /var/www/html \
    && ln -s /app/public /var/www/html \
    && a2enmod rewrite \
    && chmod 0644 /etc/cron.d/schedule-cron \
    && chmod +x /app-entrypoint.sh \
    && chmod +x /start-cron.sh \
    && touch /var/log/cron.log

# RUN composer install --no-dev

CMD ["/app-entrypoint.sh"]
