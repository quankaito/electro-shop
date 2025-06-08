# Dockerfile Cuối Cùng - Hoàn Thiện

FROM php:8.3-fpm

# === BƯỚC MỚI: Khai báo các biến sẽ được truyền vào lúc build ===
# Render sẽ tự động tìm các biến môi trường tương ứng và truyền vào đây.
ARG VITE_PUSHER_APP_KEY
ARG VITE_PUSHER_APP_CLUSTER
# Thêm các biến VITE_ khác của bạn nếu có
# ARG VITE_APP_NAME 
# ARG VITE_ASSET_URL

# Expose các biến ARG thành biến môi trường để các lệnh RUN có thể sử dụng
ENV VITE_PUSHER_APP_KEY=$VITE_PUSHER_APP_KEY
ENV VITE_PUSHER_APP_CLUSTER=$VITE_PUSHER_APP_CLUSTER
# ENV VITE_APP_NAME=$VITE_APP_NAME
# ENV VITE_ASSET_URL=$VITE_ASSET_URL

# Cài đặt các gói cần thiết
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libicu-dev libonig-dev libzip-dev zip \
    build-essential nodejs npm nginx supervisor \
    && docker-php-ext-install pdo_pgsql intl mbstring zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Cài đặt dependencies và build assets
# Bây giờ, npm run build sẽ thấy các biến VITE_*
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# Copy các file cấu hình
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY default.conf /etc/nginx/sites-enabled/default
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Cấp quyền thực thi
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]