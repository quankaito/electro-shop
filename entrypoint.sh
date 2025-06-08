#!/bin/sh
sleep 2

# === SỬA QUYỀN ===
echo "Finalizing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# === CÁC LỆNH CACHE CƠ BẢN ===
echo "Clearing and Caching configuration..."
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# === LIÊN KẾT THƯ MỤC STORAGE ===
echo "Linking storage..."
if [ -L "public/storage" ]; then
    rm "public/storage"
fi
php artisan storage:link

# === CHẠY DATABASE MIGRATIONS ===
echo "Running database migrations..."
php artisan migrate --force

# === BƯỚC MỚI: DISPATCH JOB LÀM NÓNG CACHE (chạy siêu nhanh) ===
echo "Dispatching Cloudinary cache warming job..."
php artisan tinker --execute="App\Jobs\WarmCloudinaryCacheJob::dispatch()"

# Dòng cuối cùng này sẽ thực thi lệnh được truyền vào từ Dockerfile
echo "Starting Supervisor..."
exec "$@"
