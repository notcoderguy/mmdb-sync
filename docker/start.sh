#!/bin/bash


# Run migrations and seed the database if needed (optional, can also be done at runtime)
if [ ! -f storage/db/database.sqlite ]; then
    echo "Database file not found. Creating a new one..."
    sudo touch storage/db/database.sqlite
    sudo chown -R www-data:www-data storage/db
    php artisan migrate:fresh --force
    php artisan db:seed --force
fi

php artisan app:pull:mmdb &
php artisan serve --host=0.0.0.0 --port=80
