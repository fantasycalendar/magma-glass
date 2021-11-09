#!/usr/bin/env bash
cd /app/ || exit 1;
service redis-server start
php artisan optimize
php artisan route:cache
service nginx start
php-fpm
