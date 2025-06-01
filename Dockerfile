# --- 1. Base image: PHP-FPM 8.1 ---
FROM php:8.1-fpm

# --- 2. Cài các package & extension PHP cần thiết ---
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
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# --- 3. Cài Composer từ image composer chính chủ ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- 4. Thiết lập thư mục làm việc ---
WORKDIR /var/www/html

# --- 5. Copy composer.json & composer.lock, rồi cài dependencies PHP ---
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# --- 6. Copy toàn bộ source code (theo .dockerignore) ---
COPY . .

# --- 7. Gán quyền cho storage và bootstrap/cache ---
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# --- 8. Tùy chọn: nếu không có front-end, có thể bỏ qua bước cài Node/Vite ---
# RUN apt-get update && apt-get install -y nodejs npm
# RUN npm install && npm run build

# --- 9. Expose port (artisant serve sẽ dùng port này) ---
EXPOSE 9000

# --- 10. Migrate & serve ---
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=9000"]
