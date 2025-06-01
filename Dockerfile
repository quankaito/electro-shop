# -------------------------------
# 1. SỬ DỤNG PHP 8.2-FPM LÀM BASE
# -------------------------------
FROM php:8.2-fpm

# -------------------------------
# 2. CÀI ĐẶT CÁC GÓI HỆ THỐNG CẦN THIẾT
#    - libpq-dev: để cài pdo_pgsql (PostgreSQL)
#    - libicu-dev: để cài ext-intl
#    - libonig-dev: để cài mbstring
#    - libzip-dev: để cài zip
#    - build-essential (g++/gcc) dùng khi npm build (nếu có module native)
#    - nodejs & npm: để build frontend (Vite/Mix)
#    - git, unzip, curl: công cụ hỗ trợ Composer, Git
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
# 3. TẢI SẴN COMPOSER TỪ IMAGE CHÍNH CHỦ
# -------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------------
# 4. CHỌN THƯ MỤC LÀM VIỆC
# -------------------------------
WORKDIR /var/www/html

# -------------------------------
# 5. COPY composer.json VÀ composer.lock → CHẠY composer install
#    (để tận dụng cache của Docker layer: nếu chỉ package PHP thay đổi, 
#     Docker sẽ không phải tải lại toàn bộ dependencies)
# -------------------------------
COPY composer.json composer.lock ./
# Nếu project có script "post-install-cmd": ["@php artisan key:generate --ansi"], 
# thì Docker sẽ tự generate APP_KEY vào .env khi install.
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# -------------------------------
# 6. COPY package.json VÀ package-lock.json → CHẠY npm install & npm run build
#    (build frontend assets vào public/build hoặc public/js, public/css tùy loại)
# -------------------------------
COPY package.json package-lock.json ./
# Nếu bạn dùng Yarn thay npm, thay mệnh lệnh cho phù hợp
RUN npm install
RUN npm run build

# -------------------------------
# 7. COPY TOÀN BỘ SOURCE CODE (VÍ DỤ: app/, routes/, views/, v.v.)
#    .dockerignore nên loại trừ: vendor/, node_modules/, .env, .git, v.v.
# -------------------------------
COPY . .

# -------------------------------
# 8. PHÂN QUYỀN CHO storage VÀ bootstrap/cache
# -------------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# -------------------------------
# 9. EXPOSE PORT 9000 (PHPARTISAN SERVE SẼ LẮNG NGHE TRÊN PORT 9000)
# -------------------------------
EXPOSE 9000

# -------------------------------
# 10. KHI CONTAINER KHỞI ĐỘNG:
#    - CHỜ DATABASE SẴN SÀNG (nếu cần)
#    - TỰ ĐỘNG MIGRATE (php artisan migrate --force)
#    - CHẠY PHP ARTISAN SERVE TRÊN 0.0.0.0:9000
#
#    LƯU Ý:
#    - Nếu Postgres chưa kịp start khi đến migrate, bạn có thể thêm "sleep 5" 
#      để chờ 5 giây rồi mới migrate, ví dụ: "sleep 5 && php artisan migrate --force ..."
#    - Nếu bạn không muốn tự động migrate, chỉ chạy serve, hãy xóa phần "php artisan migrate".
# -------------------------------
CMD ["sh", "-c", "sleep 5 && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=9000"]
