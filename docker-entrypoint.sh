#!/bin/sh
set -e

# Copy env example if .env doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Ensure APP_KEY exists
if [ -z "$APP_KEY" ] && grep -q "APP_KEY=$" .env; then
    php artisan key:generate --force
fi

# Dynamically set Apache port
if [ -n "$PORT" ]; then
    echo "Configuring Apache to listen on port $PORT"
    sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf
fi

# Ensure only mpm_prefork is loaded at runtime to avoid MPM conflicts
echo "Configuring Apache MPM..."
a2dismod mpm_event mpm_worker || true
a2enmod mpm_prefork || true


# Clear and cache configurations
echo "Caching Laravel configuration..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running migrations..."
php artisan migrate --force

# Create public storage symbolic link
echo "Creating storage symlink..."
php artisan storage:link --force || true

# Execute the main container command
echo "Starting Apache..."
exec "$@"
