#!/bin/sh
set -e

# Heroku provides PORT environment variable
PORT=${PORT:-8080}
echo "Starting with PORT=$PORT"

# Replace __PORT__ placeholder in nginx config with actual port
sed -i "s/__PORT__/$PORT/g" /etc/nginx/nginx.conf

# Cache Laravel config
cd /var/www/html
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start supervisor (manages nginx, php-fpm, and reverb)
exec /usr/bin/supervisord -c /etc/supervisord.conf
