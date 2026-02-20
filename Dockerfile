FROM php:8.2-apache

# Installation PDO
RUN docker-php-ext-install pdo pdo_mysql

# SOLUTION RADICALE : On désactive event explicitement avant de lancer quoi que ce soit
RUN a2dismod mpm_event || true
RUN a2enmod mpm_prefork
RUN a2enmod rewrite

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# Installation des dépendances
RUN apt-get update && apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev

# On force explicitement une version récente du driver
RUN pecl install mongodb-1.16.0 && docker-php-ext-enable mongodb



# On simplifie le CMD au maximum
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground