
FROM php:8.3-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY --chown=www-data:www-data . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]

