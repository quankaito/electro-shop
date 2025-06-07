# 1. Chọn PHP 8.3-FPM làm base image
FROM php:8.3-fpm

# 2. Cài gói hệ thống cần thiết
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

# 3. Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Chọn thư mục làm việc
WORKDIR /var/www/html

# 5. Copy source code
COPY . .

# 6. Cài dependencies PHP
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# 7. Build frontend
RUN npm install
RUN npm run build

# 8. Phân quyền
# Chạy migrate ở đây trong lúc build nếu bạn muốn, thay vì lúc runtime
# RUN php artisan migrate --force
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# 9. Expose port 9000 (cổng mặc định của PHP-FPM)
EXPOSE 9000

# 10. KHÔNG CẦN CMD - Sẽ tự động chạy `php-fpm`
# CMD ["php-fpm"] # Dòng này đã có sẵn trong image gốc, không cần viết lại.