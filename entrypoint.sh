#!/bin/sh
sleep 2

# === CÁC LỆNH SETUP CỦA LARAVEL ===
echo "Running setup commands..."

# Xóa cache cũ để đảm bảo không có xung đột
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Cache lại cấu hình
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Liên kết storage
if [ -L "public/storage" ]; then
    rm "public/storage"
fi
php artisan storage:link

# Migrate database
php artisan migrate --force

# Dispatch job làm nóng cache Cloudinary
php artisan tinker --execute="App\Jobs\WarmCloudinaryCacheJob::dispatch()"

# === BƯỚC QUAN TRỌNG NHẤT: SỬA QUYỀN TOÀN DIỆN ===
# Chạy lệnh này cuối cùng để đảm bảo tất cả các file, kể cả file do artisan
# và npm build tạo ra, đều có đúng quyền cho user www-data.
echo "Finalizing all permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Khởi động server
echo "Starting Supervisor..."
exec "$@"