FROM php:8-fpm

COPY . /app
COPY --chown=www-data:www-data setup/nginx/app.conf /etc/nginx/conf.d/default.conf
COPY setup/entrypoint.sh /etc/entrypoint.sh

WORKDIR /var/www/html

RUN apt-get update -y \
    && apt-get install -y nginx curl gnupg \
    && curl -sL https://deb.nodesource.com/setup_20.x  | bash - \
    && apt-get update && apt-get install -y \
               nodejs \
               libfreetype6-dev \
               libjpeg62-turbo-dev \
               libmcrypt-dev \
               libpng-dev \
               libzip-dev \
               unzip \
               zip \
               git \
               redis-server\
    && apt-get clean \
    && docker-php-ext-install -j$(nproc) pdo_mysql zip \
    && pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis \
    && curl --silent --show-error https://getcomposer.org/installer | php \
    && chmod +x /etc/entrypoint.sh \
    && mkdir -p /app/storage/framework/sessions \
    && mkdir -p /app/storage/framework/views \
    && mkdir -p /app/storage/framework/cache \
    && npm install --prefix /app/ \
    && npm run build --prefix /app/ \
    && rm -rf /app/node_modules \
    && /usr/local/bin/php /var/www/html/composer.phar install -d /app/ \
    && /usr/local/bin/php /var/www/html/composer.phar dump-auto -d /app/ \
    && chown -R www-data:www-data /app \
    && chown -R www-data:www-data /var/www/ \
    && chmod -R 775 /app

WORKDIR /app

ENV APP_NAME "Magma Glass"

EXPOSE 80

ENTRYPOINT ["/etc/entrypoint.sh"]
