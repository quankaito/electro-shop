#################################################################
# Dockerfile Cuối Cùng cho Laravel trên Render
# Sử dụng Entrypoint Script và Supervisor
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
#############################################
COPY . .

#############################################
# 6. Cài dependencies PHP và chạy scripts
#############################################
RUN composer install --no-dev --optimize-autoloader

#############################################
# 7. Build frontend
#############################################
RUN npm install
RUN npm run build

#############################################
# 8. Copy file cấu hình Supervisor và script Entrypoint
#############################################
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

#############################################
# 9. Phân quyền
#    Đã xóa các lệnh cache khỏi đây.
#    Thêm quyền thực thi cho script entrypoint.
#############################################
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

#############################################
# 10. Expose PORT 9000
#############################################
EXPOSE 9000

#############################################
# 11. Script khởi động (Entrypoint)
#     Chỉ định script sẽ chạy đầu tiên khi container khởi động.
#############################################
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

#############################################
# 12. Lệnh mặc định (CMD)
#     Lệnh này sẽ được truyền vào cho ENTRYPOINT.
#     Script entrypoint sẽ chạy lệnh này sau khi hoàn tất.
#############################################
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]