#!/bin/sh
set -e

# 1. Ensure .env exists
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# 2. Ensure storage and bootstrap/cache directories are writable
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# 3. Ensure composer dependencies are installed
if [ ! -d /var/www/html/vendor ]; then
    echo "vendor directory missing. Installing composer dependencies..."
    composer install --no-interaction --prefer-dist
fi

# 4. Ensure APP_KEY is set
if ! grep -q "^APP_KEY=base64:" /var/www/html/.env; then
    echo "Generating application key..."
    php artisan key:generate --no-interaction
fi

# 5. Ensure storage symlink exists
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link || true
fi

echo "Application environment ready. Starting PHP-FPM..."
exec "$@"
