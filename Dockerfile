FROM php:8.1-apache

# Installation des dépendances PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configuration d'Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Copie des fichiers de l'application
WORKDIR /var/www/html
COPY . .

# Installation des dépendances avec Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Exposition du port 80
EXPOSE 80

# Démarrage d'Apache
CMD ["apache2-foreground"]