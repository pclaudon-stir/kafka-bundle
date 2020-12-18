FROM php:7.4-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk add build-base librdkafka-dev php7-dev && \
    pecl -q install rdkafka xdebug && \
    docker-php-ext-install pcntl && \
    docker-php-ext-enable rdkafka xdebug && \
    curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
