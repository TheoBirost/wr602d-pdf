# Dockerfile pour une application Symfony avec PHP-FPM et Nginx

# --- Étape 1: Build de l'image PHP-FPM ---
FROM php:8.2-fpm-alpine AS symfony_php

# Installer les dépendances système nécessaires
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    nginx \
    postgresql-dev \
    libpq \
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
        pdo_pgsql \
        zip \
        intl \
        gd \
        opcache \
        sodium \
        bcmath \
        exif \
        mbstring \
        mysqli \
        openssl \
        pcntl \
        pdo_mysql \
        soap \
        sockets \
        xml \
        gmp \
        imagick # Imagick est utile pour la manipulation d'images, souvent liée aux PDF

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

# --- Étape 2: Configuration de Nginx ---
FROM nginx:alpine AS symfony_nginx

# Copier la configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copier les fichiers statiques de l'application (public)
COPY --from=symfony_php /app/public /var/www/html/public

# Exposer le port 80
EXPOSE 80

# --- Étape 3: Image finale (multi-stage build) ---
FROM symfony_php

# Copier la configuration Nginx et les fichiers statiques
COPY --from=symfony_nginx /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY --from=symfony_nginx /var/www/html/public /var/www/html/public

# Configurer PHP-FPM pour écouter sur un port (par défaut 9000)
# et s'assurer que Nginx peut le joindre.
# Pour Dokploy, il est courant d'avoir PHP-FPM et Nginx dans le même conteneur ou des conteneurs séparés.
# Ici, je vais les mettre dans le même conteneur pour simplifier le déploiement sur Dokploy.

# Copier la configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Créer un script de démarrage pour Nginx et PHP-FPM
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exposer le port 80
EXPOSE 80

# Démarrer Nginx et PHP-FPM
CMD ["/usr/local/bin/entrypoint.sh"]