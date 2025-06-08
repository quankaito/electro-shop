#!/bin/sh
set -e

# Đợi database sẵn sàng
sleep 3

# Chạy các lệnh setup cơ bản (rất nhanh)
echo "Running basic setup..."
php artisan migrate --force
php artisan storage:link

# === BƯỚC QUAN TRỌNG NHẤT: GÁN QUYỀN TOÀN DIỆN ===
# Chạy cuối cùng để đảm bảo mọi file đều có đúng quyền
echo "Finalizing permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Khởi động server
echo "Starting Supervisor..."
exec "$@"