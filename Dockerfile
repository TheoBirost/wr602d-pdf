# Dockerfile pour une application Symfony avec PHP-FPM et Nginx

# --- Étape 1: Build de l'image PHP-FPM ---
FROM php:8.2-fpm-alpine AS symfony_php

# Installer les dépendances système nécessaires
# Remplacement de postgresql-dev et libpq par des dépendances neutres ou pour MariaDB/MySQL
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    nginx \
    libzip-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    gmp-dev \
    libxml2-dev \
    imagemagick-dev \
    libwebp-dev \
    supervisor \
    nodejs \
    npm

# Installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        gd \
        opcache \
        sodium \
        bcmath \
        exif \
        mbstring \
        openssl \
        pcntl \
        soap \
        sockets \
        xml \
        gmp \
        imagick

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers de l'application
COPY . /app

# Installer les dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Nettoyer le cache Symfony
RUN php bin/console cache:clear --env=prod --no-interaction
RUN php bin/console assets:install --symlink --relative public --env=prod

# Configurer les permissions
RUN chown -R www-data:www-data var public

# --- Étape 2: Image finale avec Nginx et PHP-FPM ---
FROM symfony_php

# Créer les répertoires nécessaires pour Nginx
RUN mkdir -p /run/nginx /var/log/nginx
RUN chown -R www-data:www-data /var/log/nginx

# Copier la configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copier le script de démarrage
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exposer le port 80
EXPOSE 80

# Démarrer Nginx et PHP-FPM
CMD ["/usr/local/bin/entrypoint.sh"]
