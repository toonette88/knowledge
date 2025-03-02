# Utilisation d'une image PHP avec FPM et les extensions nécessaires
FROM php:8.2-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip mbstring opcache

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installation manuelle de Symfony CLI
RUN curl -sSLo /usr/local/bin/symfony https://github.com/symfony/cli/releases/latest/download/symfony_linux_amd64 && \
    chmod +x /usr/local/bin/symfony

# Définition du répertoire de travail
WORKDIR /var/www/html

# Copie du projet
COPY . .

# Installation des dépendances PHP avec Composer
RUN composer install --no-dev --optimize-autoloader

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html/var && chmod -R 777 /var/www/html/var

# Exposition du port pour PHP-FPM
EXPOSE 9000

# Commande de démarrage
CMD ["php-fpm"]
