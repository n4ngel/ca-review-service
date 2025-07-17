FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy Xdebug config
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Create non-root user with same UID as host user (pass UID as build arg)
ARG UID=1000
ARG GID=1000
RUN groupadd -g ${GID} appuser \
    && useradd -u ${UID} -g ${GID} -m appuser

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

USER appuser

RUN git config --global --add safe.directory /var/www/html

WORKDIR /var/www/html
