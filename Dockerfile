# ใช้ PHP 8.2 FPM
FROM php:8.2-fpm

# ติดตั้ง dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git \
    libpng-dev libjpeg-dev libfreetype6-dev libldap2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd ldap

# ติดตั้ง Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ตั้งค่า working directory
WORKDIR /var/www/html

# copy project
COPY . .

# ติดตั้ง Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# ให้สิทธิ์ storage และ bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
