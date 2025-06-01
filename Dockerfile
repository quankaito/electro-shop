# -------------------------------
#  1. SỬ DỤNG PHP 8.3-FPM LÀM BASE IMAGE
# -------------------------------
FROM php:8.3-fpm

# -------------------------------
#  2. CÀI ĐẶT CÁC GÓI HỆ THỐNG CẦN THIẾT
#     - libpq-dev: để cài pdo_pgsql (PostgreSQL)
#     - libicu-dev: để cài ext-intl
#     - libonig-dev: để cài mbstring
#     - libzip-dev: để cài zip
#     - build-essential: để hỗ trợ build native modules (nếu cần)
#     - nodejs & npm: để build frontend (Vite/Mix)
#     - git, unzip, curl: hỗ trợ Composer, Git
# -------------------------------
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

# -------------------------------
#  3. COPY COMPOSER TỪ IMAGE CHÍNH CHỦ
# -------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------------
#  4. CHỌN THƯ MỤC LÀM VIỆC
# -------------------------------
WORKDIR /var/www/html

# -------------------------------
#  5. COPY composer.json VÀ composer.lock → CHẠY composer install
#     (để tận dụng cache Docker layer: nếu composer.json/composer.lock không đổi, 
#      Docker sẽ không download lại dependencies)
# -------------------------------
COPY composer.json composer.lock ./
# Nếu dự án cần tăng memory cho Composer, thêm:
# ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# -------------------------------
#  6. COPY package.json VÀ package-lock.json → CHẠY npm install & npm run build
#     (build frontend assets, ví dụ sử dụng Vite hoặc Laravel Mix)
# -------------------------------
COPY package.json package-lock.json ./
RUN npm install
RUN npm run build

# -------------------------------
#  7. COPY TOÀN BỘ SOURCE CODE (theo .dockerignore)
# -------------------------------
COPY . .

# -------------------------------
#  8. PHÂN QUYỀN CHO storage VÀ bootstrap/cache
# -------------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# -------------------------------
#  9. EXPOSE PORT 9000 (php artisan serve sẽ lắng nghe cổng này)
# -------------------------------
EXPOSE 9000

# -------------------------------
# 10. KHI CONTAINER KHỞI ĐỘNG:
#     - ĐỢI DATABASE SẴN SÀNG (sleep 5)
#     - CHẠY php artisan migrate --force (tự động tạo/migrate schema)
#     - CHẠY php artisan serve trên 0.0.0.0:9000
#     (Nếu bạn không muốn migrate tự động, bỏ phần migrate đi)
# -------------------------------
CMD ["sh", "-c", "sleep 5 && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=9000"]
