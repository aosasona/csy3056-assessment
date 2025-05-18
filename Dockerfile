FROM composer:2.8.8 AS composer

COPY . /app

RUN composer install --optimize-autoloader --no-interaction --no-progress && \
	composer dump-autoload --optimize && \
	composer migrate

FROM trafex/php-nginx:3.9.0


USER root

# Install PHP extensions for SQLite3 and PDO_SQLite
RUN apk add --no-cache php83-sqlite3 php83-pdo_sqlite

USER nobody
COPY --chown=nginx --from=composer /app /var/www/html
COPY --chown=nginx ./nginx.conf /etc/nginx/conf.d/default.conf
