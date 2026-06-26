#!/bin/bash
set -e

if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

if [ ! -d "node_modules" ]; then
    echo "Installing NPM dependencies..."
    npm install
    npm run build
fi

if [ ! -f "public/storage" ]; then
    php artisan storage:link --force 2>/dev/null || true
fi

if grep -q '^APP_KEY=$' .env 2>/dev/null || ! grep -q '^APP_KEY=[^ ]' .env 2>/dev/null; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

exec "$@"
