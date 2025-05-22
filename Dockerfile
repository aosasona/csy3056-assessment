# Use composer image to install dependencies so they can be copied to the final image
FROM composer:2.8.8 AS composer

# Copy the current directory contents into the container at /app
COPY . /app

# Install dependencies and generate autoload files
RUN composer install --optimize-autoloader --no-interaction --no-progress && \
	composer dump-autoload --optimize


# Base image for PHP and Nginx
FROM trafex/php-nginx:3.9.0

# Use nobody user to avoid permission issues
USER nobody

# Copy files from the composer image to the final image
COPY --chown=nginx --from=composer /app /var/www/html

# Copy the nginx configuration file into the container
COPY --chown=nginx ./nginx.conf /etc/nginx/conf.d/default.conf
