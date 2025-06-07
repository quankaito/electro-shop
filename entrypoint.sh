#!/bin/sh

# Chờ một chút để đảm bảo các dịch vụ khác (như database) sẵn sàng
sleep 2

# === BƯỚC 1: XÓA SẠCH TẤT CẢ CACHE ===
echo "Clearing all application caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# === BƯỚC MỚI: ĐẢM BẢO THƯ MỤC SESSION ĐÚNG QUYỀN ===
echo "Ensuring session directory exists and has correct permissions..."
mkdir -p storage/framework/sessions
chown -R www-data:www-data storage/framework/sessions
chmod -R 775 storage/framework/sessions

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

# Dòng cuối cùng này sẽ thực thi lệnh được truyền vào từ Dockerfile (chính là supervisord)
echo "Starting Supervisor..."
exec "$@"