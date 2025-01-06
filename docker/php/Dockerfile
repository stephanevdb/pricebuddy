ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-apache-bookworm

RUN docker-php-ext-install exif \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install pcntl posix \
    && docker-php-ext-install mysqli pdo pdo_mysql

#    # Enable pcntl function required by horizon.
#    && sed -i 's/pcntl_signal,//g' /usr/local/etc/php/php.ini \
#    && sed -i 's/pcntl_async_signals,//g' /usr/local/etc/php/php.ini \
#    && sed -i 's/pcntl_alarm,//g' /usr/local/etc/php/php.ini

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

COPY ../.. /app

RUN rm -rf /var/www/html && \
    ln -s /app/public /var/www/html

CMD ["apache2-foreground"]
