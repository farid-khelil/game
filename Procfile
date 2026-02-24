web: php artisan reverb:start --host=127.0.0.1 --port=6001 & vendor/bin/heroku-php-nginx -C nginx.conf public/
release: php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
