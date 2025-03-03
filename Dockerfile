FROM php:8.2-fpm

# Installer les dépendances système et PHP
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libonig-dev libzip-dev zip curl nginx libicu-dev libpng-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring opcache intl gd

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && chmod +x /usr/local/bin/composer
RUN composer --version

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . .

# Nettoyer le cache Composer et Symfony
RUN composer clear-cache
RUN rm -rf var/cache/*

# Corriger les permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 777 /var/www/html

# Installer les dépendances Symfony en production
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Copier la configuration Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Exposer le port HTTP 80
EXPOSE 80

# Démarrer Nginx et PHP-FPM
CMD service nginx start && php-fpm
