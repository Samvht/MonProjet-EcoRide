FROM php:8.4-apache

#installation dépendance pour mongodb
RUN apt-get update && apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev \
    &&pecl install mongodb \
    && docker-php-ext-enable mongodb
