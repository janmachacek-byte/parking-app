#!/usr/bin/env sh
set -e

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

php artisan migrate --force || true

exec supervisord -c /etc/supervisord.conf
