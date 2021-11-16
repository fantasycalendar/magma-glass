#!/usr/bin/env bash
cd /app/ || exit 1;

SSH_KEY=/app/storage/app/id_rsa
if test -f "$SSH_KEY"; then
    echo "$SSH_KEY exists."
else
    echo "Generating Github Deployment key..."
    ssh-keygen -N '' -t ed25519 -C "Magma Glass Key" -f /app/storage/app/id_rsa
    echo "Github deployment key generated! Starting services."
fi


service redis-server start
php artisan optimize
php artisan route:cache
php artisan queue:listen &
service nginx start
php-fpm
