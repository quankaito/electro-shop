#################################################################
# Dockerfile Hoàn Chỉnh cho Laravel trên Render (Đã sửa lỗi)
# Chạy PHP-FPM và Queue Worker với Supervisor
#################################################################

#############################################
# 1. Chọn PHP 8.3-FPM làm base image
#############################################
FROM php:8.3-fpm

# Đặt biến môi trường để tắt các thông báo tương tác khi cài gói
ENV DEBIAN_FRONTEND=noninteractive

#############################################
# 2. Cài gói hệ thống cần thiết + SUPERVISOR
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
    supervisor \
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
# 5. Copy TOÀN BỘ source code
#    Lưu ý: Bạn nên có file .dockerignore
#############################################
COPY . .

#############################################
# 6. Cài dependencies PHP và chạy scripts (Gộp làm một)
#    Đây là cách làm chuẩn và đáng tin cậy.
#############################################
RUN composer install --no-dev --optimize-autoloader

#############################################
# 7. Build frontend (npm install + npm run build)
#############################################
RUN npm install
RUN npm run build

#############################################
# 8. Copy file cấu hình của Supervisor vào image
#############################################
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

#############################################
# 9. Tối ưu hóa Laravel cho Production và Phân quyền
#    Lưu ý: Không tạo APP_KEY ở đây, hãy đặt nó trong Environment Variables của Render
#############################################
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

#############################################
# 10. Expose PORT 9000 (cổng PHP-FPM lắng nghe)
#############################################
EXPOSE 9000

#############################################
# 11. Lệnh khởi động cuối cùng
#     Chạy Supervisor, nó sẽ tự động khởi động php-fpm và queue worker
#############################################
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]