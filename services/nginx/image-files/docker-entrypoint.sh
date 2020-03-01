#!/usr/bin/env sh
set -eu

envsubst '${NGINX_PORT} ${RESOLVER_IP} ${UPSTREAM_HOST} ${UPSTREAM_PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

exec "$@"
