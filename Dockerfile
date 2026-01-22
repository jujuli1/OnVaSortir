FROM php:8.4-fpm

# Installer les dépendances système et extensions PHP pour MySQL et Symfony
RUN apt-get update && apt-get install -y \
    default-mysql-client libzip-dev unzip git \
    && docker-php-ext-install pdo_mysql zip

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Copier le projet
COPY . .

# Installer les dépendances Symfony
RUN composer install

EXPOSE 9000
CMD ["php-fpm"]
