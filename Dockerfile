FROM cultureweb/symfony:5-php-8.1-v1.0

RUN pecl install redis-5.3.7 && docker-php-ext-enable redis

RUN mkdir -p /var/www/html/var \
    && chown -R www-data:www-data /var/www/html/var \
    && chmod -R 775 /var/www/html/var
