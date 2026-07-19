#!/bin/bash
set -e

echo '=== STEP 1: Setup directory ==='
mkdir -p /var/www/hrm
cd /var/www/hrm

echo '=== STEP 2: Git clone/pull ==='
if [ ! -d .git ]; then
    echo 'Fresh clone...'
    rm -rf /var/www/hrm
    git clone https://github.com/icfo-bookme/HRM.git /var/www/hrm
    cd /var/www/hrm
else
    echo 'Fetch and reset...'
    git fetch --all
    git reset --hard origin/main
    git clean -fd
fi

echo '=== STEP 3: Create .env (AFTER git clean) ==='
cp -f .env.example .env
echo '.env created:'
ls -la .env

echo '=== STEP 4: Generate APP_KEY ==='
/usr/bin/php8.4 artisan key:generate --force

echo '=== STEP 5: Update .env values ==='
sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env
sed -i 's|APP_URL=.*|APP_URL=http://187.77.128.105:8001|' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=hrmv1/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=hrm_user/' .env

echo '=== STEP 6: Verify .env ==='
head -6 .env

echo '=== STEP 7: Clear cache ==='
/usr/bin/php8.4 artisan config:clear
/usr/bin/php8.4 artisan route:clear
/usr/bin/php8.4 artisan view:clear

echo '=== STEP 8: Composer install ==='
export COMPOSER_ALLOW_SUPERUSER=1
/usr/bin/php8.4 /usr/local/bin/composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo '=== STEP 9: NPM build ==='
npm ci --no-audit --no-fund 2>/dev/null || npm install --no-audit --no-fund
npm run build

echo '=== STEP 10: Migrate ==='
/usr/bin/php8.4 artisan migrate --force

echo '=== STEP 11: Permissions ==='
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo '=== ✅ DEPLOYMENT COMPLETE ==='