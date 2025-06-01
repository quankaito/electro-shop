#############################################
# 1. Chọn PHP 8.3-FPM làm base image
#############################################
FROM php:8.3-fpm

#############################################
# 2. Cài gói hệ thống cần thiết
#    - libpq-dev: để build pdo_pgsql (Postgres)
#    - libicu-dev: để build ext-intl
#    - libonig-dev: mbstring
#    - libzip-dev: zip extension
#    - build-essential: gcc/g++ (nếu frontend build cần)
#    - nodejs + npm: build frontend (Vite/Mix)
#    - git, unzip, curl: hỗ trợ Composer, Git
#############################################
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    build-essential \
    nodejs \
    npm \
    && docker-php-ext-install \
    pdo_pgsql \
    intl \
    mbstring \
    zip \
    bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

#############################################
# 3. Copy Composer từ image chính chủ
#############################################
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

#############################################
# 4. Chọn thư mục làm việc
#############################################
WORKDIR /var/www/html

#############################################
# 5. Copy TOÀN BỘ source code (bao gồm artisan, config, app, v.v.)
#    - Bước này đảm bảo file artisan đã có mặt trước khi Composer chạy.
#    - .dockerignore sẽ loại trừ vendor/, node_modules/, .env, .git, v.v.
#############################################
COPY . .

#############################################
# 6. Cài dependencies PHP qua Composer
#    - Khi chạy, Laravel post-autoload scripts (vd: artisan package:discover) sẽ hoạt động 
#      vì file artisan đã được copy ở bước 5.
#    - Nếu cần tăng memory, có thể thêm ENV COMPOSER_MEMORY_LIMIT=-1 
#############################################
RUN composer install --no-dev --optimize-autoloader --prefer-dist

#############################################
# 7. Build frontend (npm install + npm run build)
#    - Giả sử bạn có file package.json & package-lock.json ở root.
#    - Nếu bạn sử dụng Yarn, thay bằng yarn install && yarn build.
#############################################
RUN npm install
RUN npm run build

#############################################
# 8. Phân quyền cho storage và bootstrap/cache
#############################################
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

#############################################
# 9. Expose PORT 9000 (Laravel serve sẽ chạy port này)
#############################################
EXPOSE 9000

#############################################
# 10. Khi container khởi động:
#     - Đợi 5 giây để database (Postgres) có thể lên sẵn
#     - Tự động migrate database: php artisan migrate --force
#     - Serve Laravel: php artisan serve --host=0.0.0.0 --port=9000
#
# Nếu bạn không muốn migrate tự động, chỉ cần xóa phần migrate:
#   CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=9000"]
#############################################
CMD ["sh", "-c", "sleep 5 && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=9000"]
