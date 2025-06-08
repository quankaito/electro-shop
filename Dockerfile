# Dockerfile Cuối Cùng - Tối ưu và Bền vững

FROM php:8.3-fpm

# Cài đặt các gói cần thiết một lần duy nhất
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libicu-dev libonig-dev libzip-dev zip \
    build-essential nodejs npm nginx supervisor \
    && docker-php-ext-install pdo_pgsql intl mbstring zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Đặt thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ mã nguồn
COPY . .

# Cài đặt dependencies
RUN composer install --no-dev --optimize-autoloader
RUN composer dump-autoload --optimize
RUN npm install

# === BƯỚC BUILD QUAN TRỌNG NHẤT VÀ DEBUG ===
# Chạy build và ngay lập tức liệt kê các file trong thư mục build để kiểm tra
RUN echo "Running npm run build..." && npm run build && ls -la public/build

# Copy các file cấu hình
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY default.conf /etc/nginx/sites-enabled/default
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Cấp quyền thực thi cho script khởi động
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port của Nginx
EXPOSE 8080

# Đặt script khởi động
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Lệnh mặc định để chạy Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]