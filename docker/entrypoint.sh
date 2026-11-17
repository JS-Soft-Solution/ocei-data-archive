#!/bin/bash
set -e

APP_DIR="/var/www/html"

echo "============================================================"
echo " OCEI Archive â€” container bootstrap"
echo "============================================================"

# â”€â”€ 1. Copy .env if missing â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
if [ ! -f "$APP_DIR/.env" ]; then
    echo "[1/6] Copying .env.example â†’ .env"
    cp "$APP_DIR/.env.example" "$APP_DIR/.env"
else
    echo "[1/6] .env already exists â€” skipping copy"
fi

# â”€â”€ 2. Composer install â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
echo "[2/6] Running composer install..."
composer install \
    --working-dir="$APP_DIR" \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# â”€â”€ 3. Generate app key (only if not set) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CURRENT_KEY=$(grep -E "^APP_KEY=" "$APP_DIR/.env" | cut -d= -f2 | tr -d '"' | tr -d "'")
if [ -z "$CURRENT_KEY" ]; then
    echo "[3/6] Generating application key..."
    php "$APP_DIR/artisan" key:generate --force
else
    echo "[3/6] APP_KEY already set â€” skipping"
fi

# â”€â”€ 4. Fix storage permissions â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
echo "[4/6] Setting storage & bootstrap/cache permissions..."
mkdir -p "$APP_DIR/storage/logs" \
         "$APP_DIR/storage/framework/sessions" \
         "$APP_DIR/storage/framework/views" \
         "$APP_DIR/storage/framework/cache" \
         "$APP_DIR/bootstrap/cache"
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

# â”€â”€ 5. Wait for MySQL â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
echo "[5/6] Waiting for MySQL to be ready..."
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USERNAME:-ocei_user}"
DB_PASS="${DB_PASSWORD:-OceiArchive@2025}"

until mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" --silent 2>/dev/null; do
    echo "       MySQL not ready yet â€” retrying in 3s..."
    sleep 3
done
echo "       MySQL is ready!"

# â”€â”€ 6. Migrate & seed â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
echo "[6/6] Running migrations..."
php "$APP_DIR/artisan" migrate --force

# Seed only if the users table is empty (idempotent)
USER_COUNT=$(php "$APP_DIR/artisan" tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1 || echo "0")
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "       Seeding database..."
    php "$APP_DIR/artisan" db:seed --force
else
    echo "       Database already seeded (${USER_COUNT} users found) â€” skipping"
fi

echo ""
echo "============================================================"
echo " Bootstrap complete! Starting php-fpm..."
echo "============================================================"

# Hand off to the CMD (php-fpm)
exec "$@"
