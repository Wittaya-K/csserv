#!/bin/bash
set -e

# รอให้ database พร้อม (ถ้าใช้)
# php artisan wait:database

# Generate key ถ้ายังไม่มี
if [ ! -f .env ]; then
    cp .env.example .env
fi

if grep -q "APP_KEY=$" .env; then
    php artisan key:generate --force
fi

# Clear และ cache สำหรับ production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ตรวจสอบและแก้ไขสิทธิ์
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# รัน migrations (ถ้าต้องการ)
# php artisan migrate --force

# เริ่ม PHP-FPM
exec "$@"
