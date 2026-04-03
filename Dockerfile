#
# Dockerfile optimisé pour la production Symfony
#

# --- Étape 1: BUILDER ---
# Cette étape installe toutes les dépendances et construit l'application
FROM php:8.2-fpm-alpine AS builder

# Installer les dépendances de build et les extensions PHP
RUN apk add --no-cache \
    # Dépendances de build qui seront supprimées dans l'image finale
    git curl unzip autoconf g++ make \
    # Dépendances d'exécution nécessaires pour les extensions
    libzip-dev icu-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    oniguruma-dev gmp-dev libxml2-dev imagemagick-dev libwebp-dev \
    libsodium-dev \
    nodejs npm

# Installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql mysqli zip intl gd opcache sodium bcmath exif \
    mbstring openssl pcntl soap sockets xml gmp
RUN pecl install imagick
RUN docker-php-ext-enable imagick

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier uniquement les fichiers nécessaires pour installer les dépendances
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader

# Copier le reste de l'application
COPY . .

# Exécuter les scripts Symfony et compiler les optimisations
RUN composer dump-env prod
RUN composer run-script post-install-cmd
RUN php bin/console cache:clear --env=prod --no-warmup
RUN php bin/console cache:warmup --env=prod

# --- Étape 2: FINALE ---
# Cette étape crée l'image finale, légère et sécurisée
FROM php:8.2-fpm-alpine

# Installer uniquement les dépendances d'exécution nécessaires
RUN apk add --no-cache \
    nginx \
    libzip icu-libs libpng libjpeg-turbo freetype oniguruma gmp libxml2 \
    imagemagick libwebp libsodium

# Copier les extensions PHP depuis l'étape de build
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
# Copier les configurations des extensions
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

WORKDIR /app

# Copier l'application compilée depuis l'étape de build
COPY --from=builder /app .

# Configurer les permissions
RUN chown -R www-data:www-data var public

# Copier la configuration Nginx et le script de démarrage
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]
