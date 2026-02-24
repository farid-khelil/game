#!/bin/bash
# Map Heroku's PORT to serversideup's nginx listen port
export NGINX_LISTEN_PORT=${PORT:-8080}

# Run the original S6 init
exec /init
