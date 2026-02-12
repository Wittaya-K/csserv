# ใช้ PHP 8.2 FPM
FROM php:8.2-fpm

# ติดตั้ง dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git \
    libpng-dev libjpeg-dev libfreetype6-dev libldap2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd ldap \
    && rm -rf /var/lib/apt/lists/*

# ติดตั้ง Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ตั้งค่า working directory
WORKDIR /var/www/html

# Copy composer files ก่อนเพื่อใช้ Docker cache
COPY composer.json composer.lock ./

# ติดตั้ง Laravel dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-autoloader

# Copy project files
COPY . .

# Generate autoloader อีกครั้งหลัง copy ไฟล์ทั้งหมด
RUN composer dump-autoload --optimize --no-dev

# Copy PHP-FPM config
COPY php-fpm.conf /usr/local/etc/php-fpm.d/zz-custom.conf

# สร้าง directory ที่จำเป็นถ้ายังไม่มี
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# ให้สิทธิ์ storage และ bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear และ cache Laravel (ถ้ามี .env ในขั้นตอน build)
# หมายเหตุ: key:generate ควรทำตอน runtime ไม่ใช่ build time
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan cache:clear \
    && php artisan view:clear

# สำหรับ production แนะนำให้ cache หลังจากมี .env แล้ว
# คำสั่งเหล่านี้ควรรันหลัง container start
# RUN php artisan config:cache \
#     && php artisan route:cache \
#     && php artisan view:cache

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]1~# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
