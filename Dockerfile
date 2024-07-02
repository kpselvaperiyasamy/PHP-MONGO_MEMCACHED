FROM php:8.0-apache

RUN apt update
RUN apt install libcurl4-openssl-dev pkg-config libssl-dev unzip libmemcached-dev zlib1g-dev -y

# Install MongoDB extension
RUN pecl install mongodb
RUN docker-php-ext-enable mongodb

# Install Memcached extension
RUN pecl install memcached
RUN docker-php-ext-enable memcached

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install MongoDB PHP library
RUN composer require --working-dir=/var/www mongodb/mongodb

# Copy application source
COPY . /var/www/html

EXPOSE 80
