# syntax=docker/dockerfile:1

ARG FRANKENPHP_VERSION=1
ARG PHP_VERSION=8.4
ARG NODE_VERSION=22

# -----------------------------------------------------------------------------
# base: FrankenPHP (Caddy + PHP in a single binary) shared by every PHP stage
# -----------------------------------------------------------------------------
FROM dunglas/frankenphp:${FRANKENPHP_VERSION}-php${PHP_VERSION} AS base

WORKDIR /app

RUN install-php-extensions bcmath intl opcache pcntl pdo_pgsql \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php/app.ini "$PHP_INI_DIR/conf.d/zz-app.ini"
COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/entrypoint

# Production never re-checks PHP files for changes; docker-compose flips this to 1.
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0

# -----------------------------------------------------------------------------
# vendor: production Composer dependencies (no dev packages)
# -----------------------------------------------------------------------------
FROM base AS vendor

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

# -----------------------------------------------------------------------------
# Stage 1 - assets: build the React/Inertia bundle with Vite
# -----------------------------------------------------------------------------
FROM node:${NODE_VERSION}-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json .npmrc ./
RUN npm ci --legacy-peer-deps

# tsc resolves the `ziggy-js` types from the Composer package (see tsconfig.json paths).
COPY --from=vendor /app/vendor/tightenco/ziggy ./vendor/tightenco/ziggy
COPY vite.config.js tsconfig.json tailwind.config.js postcss.config.js ./
COPY resources ./resources

RUN npm run build

# -----------------------------------------------------------------------------
# Stage 2 - production: app code + vendor + built assets on FrankenPHP
# -----------------------------------------------------------------------------
FROM base AS production

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN --mount=type=bind,from=composer:2,source=/usr/bin/composer,target=/usr/bin/composer \
    composer dump-autoload --optimize --no-dev --no-interaction \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache /data/caddy /config/caddy

# Run as an unprivileged user; port 8080 needs no root to bind.
USER www-data

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS "http://127.0.0.1:${PORT:-8080}/up" > /dev/null || exit 1

ENTRYPOINT ["entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
