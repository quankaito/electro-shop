# 1. Start từ image PHP-FPM (có thể dùng 8.1, 8.0, tùy phiên bản Laravel của bạn)
FROM php:8.1-fpm

# 2. Cài đặt các gói hệ thống cần thiết:
#    - libpq-dev: để cài pdo_pgsql (PostgreSQL)
#    - libicu-dev: để build ext-intl
#    - libonig-dev: để build mbstring
#    - libzip-dev: để build zip
#    - git, unzip, curl: công cụ phục vụ Composer / Git
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install \
    pdo_pgsql \
    intl \
    mbstring \
    zip \
    bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 3. Copy sẵn Composer từ image composer chính chủ (đã bao gồm PHP nhanh gọn)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Tạo thư mục làm việc và copy composer files trước (để tận dụng cache)
WORKDIR /var/www/html
COPY composer.json composer.lock ./

# 5. Cài đặt các dependency PHP thông qua Composer
#    --no-dev: tránh cài package dev
#    --optimize-autoloader: tối ưu autoloader
#    --prefer-dist: ưu tiên tải package bản nén
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# 6. Copy toàn bộ source code của bạn (trừ những mục .dockerignore)
COPY . .

# 7. Cho phép PHP ghi vào storage và cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# 8. (Tùy chọn) Nếu bạn có front-end (Vite/Mix), bạn có thể cài Node và build ngay trong Docker:
#    RUN apt-get update && apt-get install -y nodejs npm
#    RUN npm install && npm run build

# 9. Expose cổng 9000 (artisan serve hoặc php-fpm sẽ chạy trên port này)
EXPOSE 9000

# 10. Cuối cùng: migrate database và start Laravel bằng artisan serve
#     - Lệnh php artisan migrate --force sẽ tự tạo bảng
#     - Sau đó, chạy artisan serve để lắng nghe 0.0.0.0:9000
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=9000"]
