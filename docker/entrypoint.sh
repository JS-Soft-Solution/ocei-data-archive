#!/bin/bash
set -e

APP_DIR="/var/www/html"

echo "============================================================"
echo " OCEI Archive — Container Bootstrap"
echo "============================================================"

# 1. Copy .env if missing
if [ ! -f "$APP_DIR/.env" ]; then
    echo "[1/7] Copying .env.example -> .env"
    cp "$APP_DIR/.env.example" "$APP_DIR/.env"
else
    echo "[1/7] .env already exists - skipping copy"
fi

# 2. Composer install (only if vendor/autoload.php is missing)
if [ ! -f "$APP_DIR/vendor/autoload.php" ]; then
    echo "[2/7] Vendor missing. Running composer install..."
    composer install \
        --working-dir="$APP_DIR" \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader
else
    echo "[2/7] Vendor autoload present - skipping composer install."
fi

# 3. Generate app key (only if empty)
CURRENT_KEY=$(grep -E "^APP_KEY=" "$APP_DIR/.env" | cut -d= -f2 | tr -d '"' | tr -d "'")
if [ -z "$CURRENT_KEY" ]; then
    echo "[3/7] Generating application key..."
    php "$APP_DIR/artisan" key:generate --force
else
    echo "[3/7] APP_KEY already set - skipping"
fi

# 4. Fix storage & cache permissions
echo "[4/7] Ensuring storage and bootstrap/cache permissions..."
mkdir -p "$APP_DIR/storage/logs" \
         "$APP_DIR/storage/framework/sessions" \
         "$APP_DIR/storage/framework/views" \
         "$APP_DIR/storage/framework/cache" \
         "$APP_DIR/storage/app/public" \
         "$APP_DIR/bootstrap/cache"
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

# 5. Wait for MySQL to be fully ready
echo "[5/7] Waiting for MySQL service ($DB_HOST)..."
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USERNAME:-ocei_user}"
DB_PASS="${DB_PASSWORD:-OceiArchive@2025}"

until mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" --skip-ssl --silent 2>/dev/null; do
    echo "       MySQL not ready yet - retrying in 2s..."
    sleep 2
done
echo "       MySQL is ready and accepting connections!"

# 6. Run database migrations
echo "[6/7] Running database migrations..."
php "$APP_DIR/artisan" migrate --force --no-interaction

# 7. Seed database if users table is empty
echo "[7/7] Checking database seed status..."
USER_COUNT=$(php -r "require '$APP_DIR/vendor/autoload.php'; \$app = require_once '$APP_DIR/bootstrap/app.php'; \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class); \$kernel->bootstrap(); echo \Illuminate\Support\Facades\Schema::hasTable('users') ? \App\Models\User::count() : 0;" 2>/dev/null || echo "0")

if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "       Seeding default roles and test users..."
    php "$APP_DIR/artisan" db:seed --force --no-interaction
    echo "       Seeding completed successfully!"
else
    echo "       Database already seeded (${USER_COUNT} users found) - skipping seed."
fi

# Ensure storage symlink exists
php "$APP_DIR/artisan" storage:link --force 2>/dev/null || true

echo "============================================================"
echo " OCEI Archive ready! Starting PHP-FPM..."
echo "============================================================"

exec "$@"