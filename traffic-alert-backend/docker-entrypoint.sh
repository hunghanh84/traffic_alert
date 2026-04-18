#!/bin/sh
set -e

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    echo "   MySQL not ready yet, retrying in 3s..."
    sleep 3
done
echo "✅ MySQL is ready!"

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --no-interaction
fi

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Run migrations
php artisan migrate --force --no-interaction

# Clear view/route cache
php artisan view:clear
php artisan route:cache

echo "🚀 Starting Laravel application..."
exec "$@"
