#!/bin/sh

# Chờ một chút để đảm bảo các dịch vụ khác (như database) sẵn sàng (tùy chọn)
sleep 2

# Chạy các lệnh cache của Laravel
# Vì script này chạy lúc container khởi động, nó sẽ có quyền truy cập
# vào các biến môi trường từ Render.
echo "Running cache commands..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Chạy database migrations
echo "Running database migrations..."
php artisan migrate --force

# Dòng cuối cùng này sẽ thực thi lệnh được truyền vào từ Dockerfile (chính là supervisord)
# Nó sẽ thay thế tiến trình của script này bằng tiến trình supervisord.
exec "$@"