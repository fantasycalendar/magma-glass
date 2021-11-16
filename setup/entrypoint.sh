#!/usr/bin/env bash
cd /app/ || exit 1;

SSH_KEYFILE=/app/storage/app/id_rsa
if test -f "$SSH_KEYFILE"; then
    echo "$SSH_KEYFILE exists."
    echo "Verifying file permissions..."

    fuid="$(stat --format '%u' "$SSH_KEYFILE")"
    cuid=$(id -u)
    if [[ $cuid -eq 0 ]]; then
        cuid=33
    fi

    echo "File is owned by ID $fuid"

    if [ "${fuid}" = "${cuid}" ]; then
        echo "File is owned by current user ${cuid}. Moving on."
    else
        echo "Key file exists, but is not owned by current user ${cuid}. Please fix that."
        exit 1;
    fi
else
    echo "Generating Github Deployment key..."
    ssh-keygen -N '' -t ed25519 -C "Magma Glass Key" -f "$SSH_KEYFILE"
    chown www-data:www-data "$SSH_KEYFILE"
    chown www-data:www-data "$SSH_KEYFILE.pub"
    echo "Github deployment key generated! Starting services."
fi

echo "Creating sqlite database"
touch database/database.sqlite
php artisan migrate --force

service redis-server start

php artisan github:source-latest
php artisan optimize
php artisan route:cache
php artisan queue:listen &
service nginx start
php-fpm
