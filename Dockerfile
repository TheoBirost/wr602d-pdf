#
# Dockerfile optimisé pour la production Symfony
#

# --- Étape 1: BUILDER ---
# Cette étape installe toutes les dépendances et construit l'application
FROM php:8.2-fpm-alpine AS builder

# Installer les dépendances de base et les outils de build
RUN apk add --no-cache \
    git curl unzip autoconf g++ make \
    nodejs npm

# Installer l'outil "php-extension-installer" pour une gestion robuste des extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# Installer toutes les extensions PHP nécessaires en une seule fois
# Cet outil gère automatiquement les dépendances système (apk add)
RUN install-php-extensions \
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
    supervisor \
    # Dépendances pour les extensions installées
    libzip icu-libs libpng libjpeg-turbo freetype oniguruma gmp libxml2 \
    imagemagick libwebp libsodium

# Copier les extensions PHP et leur configuration depuis l'étape de build
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

WORKDIR /app

# Copier l'application compilée depuis l'étape de build
COPY --from=builder /app .

# Configurer les permissions
RUN chown -R www-data:www-data var public

# Copier les configurations pour Nginx et Supervisor
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/app.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

# Démarrer Supervisor pour gérer Nginx et PHP-FPM
CMD ["/usr/local/bin/entrypoint.sh"]
