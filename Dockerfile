# --- 1. Chọn image gốc có sẵn PHP với Composer ---
FROM php:8.1-fpm

# --- 2. Cài các extension cần thiết và công cụ build ---
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath

# --- 3. Cài Composer (nếu image gốc không có sẵn) ---
#    (Trên image php:8.1-fpm, chưa chắc đã có composer, nên ta tự tải)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- 4. Tạo thư mục làm việc và copy code vào container ---
WORKDIR /var/www/html

# Copy toàn bộ file composer* lên trước để tận dụng cache
COPY composer.json composer.lock ./

# Cài đặt dependencies PHP (composer)
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Copy nốt toàn bộ source code (trừ các mục trong .dockerignore)
COPY . .

# --- 5. Thiết lập quyền cho storage và bootstrap/cache ---
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# --- 6. Build front-end (nếu bạn dùng Vite / Mix / npm) ---
# Nếu có file package.json ở root và bạn dùng Vite/Mix, hãy thêm:
RUN apt-get update && apt-get install -y nodejs npm
RUN npm install && npm run build

# --- 7. Expose port mà PHP-FPM hoặc artisan serve sẽ chạy ---
EXPOSE 9000

# --- 8. Start Command mặc định: chạy PHP-FPM (có thể chạy artisan serve) ---
#    Ở đây ta dùng PHP-FPM trực tiếp, ta sẽ dùng một webserver (Nginx) làm proxy
#    Nhưng vì Render sẽ gọi theo "Start Command" sau, ta sẽ hướng dẫn dùng artisan serve.
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=9000"]
