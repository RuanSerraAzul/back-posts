#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-app}
env=${APP_ENV:-production}

if [ "$env" != "local" ]; then
    echo "Caching configuration..."
    (cd /app && php artisan config:cache && php artisan route:cache && php artisan view:cache)
fi

if [ "$role" = "app" ]; then

    echo "Is app"
    php-fpm -F -R

elif [ "$role" = "queue" ]; then

    echo "Queue role"
    php /app/artisan queue:work --verbose --timeout=0
    #php /app/artisan queue:work --verbose --tries=3 --timeout=90

elif [ "$role" = "scheduler" ]; then
    echo "Running scheduler"

    while [ true ]
    do
      php /app/artisan schedule:run --verbose --no-interaction &
      sleep 60
    done

else
    echo "Could not match the container role \"$role\""
    exit 1
fi

composer install
npm install
npm run dev &