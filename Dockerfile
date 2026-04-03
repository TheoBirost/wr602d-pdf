# --- Étape 1: BUILDER ---
FROM php:8.2-fpm-alpine AS builder

RUN apk add --no-cache \
    git curl unzip autoconf g++ make \
    nodejs npm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions \
    pdo_mysql mysqli zip intl gd opcache sodium \
    bcmath exif mbstring openssl pcntl soap sockets \
    xml gmp imagick

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader

COPY . .

# Valeur factice pour le build — remplacée par la vraie valeur au runtime
ARG DEFAULT_URI=https://localhost
ENV DEFAULT_URI=${DEFAULT_URI}

RUN composer dump-env prod

# Ne pas lancer post-install-cmd ici : il relance cache:clear
# et les scripts auto (asset-map etc.) nécessitent des vars runtime.
# On lance uniquement ce qui est nécessaire au build :
RUN php bin/console cache:clear --env=prod --no-warmup
RUN php bin/console cache:warmup --env=prod

# --- Étape 2: FINALE ---
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx supervisor \
    libzip icu-libs libpng libjpeg-turbo freetype oniguruma gmp libxml2 \
    imagemagick libwebp libsodium

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

WORKDIR /app
COPY --from=builder /app .

RUN chown -R www-data:www-data var public

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/app.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
CMD ["/usr/local/bin/entrypoint.sh"]