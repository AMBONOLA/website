#!/bin/sh
set -e

# Config is cached at start-up (not build time) so it picks up the runtime environment variables.
if [ "${APP_ENV:-production}" = "production" ]; then
    php artisan optimize
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
