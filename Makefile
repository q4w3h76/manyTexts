init:
    composer install; chown -R www-data:www-data /var/www/storage; php artisan key:generate
