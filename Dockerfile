#################################################################
# Dockerfile CUỐI CÙNG cho Laravel trên Render
# Chạy Nginx + PHP-FPM + Queue Worker với Supervisor
#################################################################

#############################################
# 1. Chọn PHP 8.3-FPM làm base image
#############################################
FROM php:8.3-fpm

ENV DEBIAN_FRONTEND=noninteractive

#############################################
# 2. Cài gói hệ thống + NGINX + SUPERVISOR
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
    nginx \
    supervisor \
    && docker-php-ext-install pdo_pgsql intl mbstring zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

#############################################
# 3. Copy Composer
#############################################
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

#############################################
# 4. Chọn thư mục làm việc
#############################################
WORKDIR /var/www/html

#############################################
# 5. Copy source code
#############################################
COPY . .

#############################################
# 6. Cài dependencies PHP
#############################################
RUN composer install --no-dev --optimize-autoloader
RUN composer dump-autoload --optimize

#############################################
# 7. Build frontend
#############################################
RUN npm install
# RUN npm run build

#############################################
# 8. Copy các file cấu hình
#############################################
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY default.conf /etc/nginx/sites-enabled/default
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

#############################################
# 9. Phân quyền
#############################################
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

#############################################
# 10. Expose PORT 8080 (Port mà Nginx sẽ lắng nghe)
#############################################
EXPOSE 8080

#############################################
# 11. Script khởi động (Entrypoint)
#############################################
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

#############################################
# 12. Lệnh mặc định (CMD)
#############################################
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]