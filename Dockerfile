# Build frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app

# Define build args for Vite env vars
ARG VITE_REVERB_APP_KEY=my-app-key
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT=443
ARG VITE_REVERB_SCHEME=https

ENV VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY
ENV VITE_REVERB_HOST=$VITE_REVERB_HOST
ENV VITE_REVERB_PORT=$VITE_REVERB_PORT
ENV VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME

COPY package*.json ./
RUN npm ci
COPY resources/ resources/
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

# Install PHP dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Final production image
FROM php:8.4-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    && docker-php-ext-install pdo_mysql pcntl

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=frontend /app/public/build public/build
COPY --chown=www-data:www-data --from=vendor /app/vendor vendor

# Create necessary directories
RUN mkdir -p /run/nginx /var/log/nginx /var/log/supervisor \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy configuration files
COPY docker/nginx-heroku.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
