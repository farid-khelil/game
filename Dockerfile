# Build frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app

# Define build args for Vite env vars
# For Heroku: WebSocket goes through same host/port as web (proxied by nginx)
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

# Laravel container with nginx + PHP-FPM + Reverb
FROM serversideup/php:8.4-fpm-nginx

WORKDIR /var/www/html

# Copy application
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=frontend /app/public/build public/build
COPY --chown=www-data:www-data --from=vendor /app/vendor vendor

# Add custom nginx config for WebSocket proxy
USER root
COPY docker/nginx-websocket.conf /etc/nginx/site-opts.d/websocket.conf

# Add Reverb as S6 service (runs on internal port 6001)
RUN mkdir -p /etc/s6-overlay/s6-rc.d/reverb /etc/s6-overlay/s6-rc.d/user/contents.d
COPY docker/reverb-run /etc/s6-overlay/s6-rc.d/reverb/run
RUN echo "longrun" > /etc/s6-overlay/s6-rc.d/reverb/type && \
    chmod +x /etc/s6-overlay/s6-rc.d/reverb/run && \
    touch /etc/s6-overlay/s6-rc.d/user/contents.d/reverb

# Heroku entrypoint to set dynamic PORT
COPY docker/heroku-entrypoint.sh /heroku-entrypoint.sh
RUN chmod +x /heroku-entrypoint.sh

# Single port exposed - nginx handles both HTTP and WebSocket
EXPOSE 8080

ENTRYPOINT ["/heroku-entrypoint.sh"]