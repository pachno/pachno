FROM php:7.4-apache

RUN apt update
RUN apt install -y zip git libicu-dev libmariadb-dev libsqlite3-dev build-essential libzip-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev libpq-dev

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt install -y nodejs

# Use either of those for sensible default options
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Most of these are needed / nice to have
RUN docker-php-ext-configure intl
RUN docker-php-ext-install -j$(nproc) intl
RUN docker-php-ext-enable intl
RUN docker-php-ext-configure pdo_mysql 
RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN docker-php-ext-enable pdo_mysql
RUN docker-php-ext-configure pdo_sqlite 
RUN docker-php-ext-install -j$(nproc) pdo_sqlite
RUN docker-php-ext-enable pdo_sqlite
RUN docker-php-ext-configure zip 
RUN docker-php-ext-install -j$(nproc) zip
RUN docker-php-ext-enable zip
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-enable gd
RUN pecl install apcu
RUN docker-php-ext-enable apcu
RUN docker-php-ext-configure pdo_pgsql 
RUN docker-php-ext-install -j$(nproc) pdo_pgsql
RUN docker-php-ext-enable pdo_pgsql
           
# run the dependancy installation
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Pachno setup
COPY --chown=www-data:www-data ./ /var/www/html/
RUN chown www-data:www-data /var/www /var/www/html

USER www-data

# Hack for setups where npm uses git+ssh instead of git+https
RUN git config --global url."https://github.com/".insteadOf ssh://git@github.com/

WORKDIR /var/www/html/
RUN composer install
RUN npm ci
RUN node_modules/.bin/grunt

# Setup
# TODO: Make it customizable through environment variables?
COPY --chown=www-data:www-data docker/b2db.yml /var/www/html/core/config/b2db.yml
COPY --chown=www-data:www-data docker/htaccess /var/www/html/public/.htaccess

# Apache setup stuff
USER root
RUN a2enmod rewrite
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf