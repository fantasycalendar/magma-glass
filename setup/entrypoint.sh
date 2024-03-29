#!/usr/bin/env bash
cd /app/ || exit 2;

mkdir -p /app/storage/app
mkdir -p /app/storage/logs
chown www-data:www-data /app/storage/logs

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

service redis-server start

echo "Creating sqlite database"
runuser -u www-data -- touch database/database.sqlite
runuser -u www-data -- php artisan migrate --force

runuser -u www-data -- php artisan github:source-latest
runuser -u www-data -- php artisan optimize
runuser -u www-data -- php artisan route:cache
runuser -u www-data -- php artisan queue:listen &

service nginx start
php-fpm
