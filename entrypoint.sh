#!/bin/sh

# Chờ một chút để đảm bảo các dịch vụ khác (như database) sẵn sàng
sleep 2

# === BƯỚC MỚI: BUILD JAVASCRIPT TẠI ĐÂY ===
# Tại thời điểm này, các biến môi trường VITE_* đã tồn tại.
echo "Running npm run build..."
npm run build

# Chạy các lệnh cache của Laravel
echo "Running cache commands..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Chạy database migrations
echo "Running database migrations..."
php artisan migrate --force

# Dòng cuối cùng này sẽ thực thi lệnh được truyền vào từ Dockerfile (chính là supervisord)
exec "$@"