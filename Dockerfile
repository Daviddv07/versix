FROM php:8.3-apache
RUN apt-get update && apt-get install -y --no-install-recommends libpng-dev libjpeg62-turbo-dev libwebp-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install mysqli gd \
    && rm -rf /var/lib/apt/lists/*
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/versix.ini
WORKDIR /var/www/html
COPY public/ public/
COPY src/ src/
COPY config/config.example.php config/config.example.php
RUN mkdir -p storage/covers && chown -R www-data:www-data storage
