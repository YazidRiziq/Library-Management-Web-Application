# PHP-FPM untuk Nginx
FROM php:8.2-fpm
# Install MySQL extensions
RUN docker-php-ext-install mysqli pdo_mysql
# Set working directory
WORKDIR /var/www/html
# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
CMD ["php-fpm"]