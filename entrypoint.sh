#!/bin/sh

# Chờ một chút để đảm bảo các dịch vụ khác (như database) sẵn sàng
sleep 2

# === BƯỚC 1: XÓA SẠCH TẤT CẢ CACHE CŨ ===
echo "Clearing all application caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# === BƯỚC 2: BUILD JAVASCRIPT ===
echo "Running npm run build..."
npm run build

# === BƯỚC 3: CACHE LẠI CẤU HÌNH MỚI ===
echo "Caching new configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# === BƯỚC 4: LIÊN KẾT THƯ MỤC STORAGE ===
echo "Linking storage..."
if [ -L "public/storage" ]; then
    rm "public/storage"
fi
php artisan storage:link

# === BƯỚC 5: CHẠY DATABASE MIGRATIONS ===
echo "Running database migrations..."
php artisan migrate --force

# === BƯỚC MỚI: LÀM NÓNG CACHE CLOUDINARY ===
echo "Warming Cloudinary URL cache..."
php artisan app:warm-cloudinary-cache

# === CACHE LẠI CẤU HÌNH CUỐI CÙNG ===
echo "Caching final configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# === BƯỚC CUỐI CÙNG & QUAN TRỌNG NHẤT: SỬA QUYỀN ===
# Chạy lệnh này cuối cùng để đảm bảo tất cả các file (kể cả file cache mới tạo)
# đều có đúng quyền cho user www-data.
echo "Finalizing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Dòng cuối cùng này sẽ thực thi lệnh được truyền vào từ Dockerfile
echo "Starting Supervisor..."
exec "$@"