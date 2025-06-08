<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

if (! function_exists('cloudinary_url')) {
    /**
     * Lấy URL từ Cloudinary và cache lại vĩnh viễn để tránh bị rate limit.
     *
     * @param string|null $path
     * @return string
     */
    function cloudinary_url(?string $path): string
    {
        // Trả về chuỗi rỗng nếu đường dẫn không hợp lệ
        if (empty($path)) {
            return '';
        }

        // Tạo một khóa cache duy nhất cho mỗi đường dẫn ảnh
        $cacheKey = 'cloudinary.url.' . md5($path);

        // Dùng Cache::rememberForever để lấy và lưu trữ URL
        // Lần đầu tiên chạy, nó sẽ gọi API Cloudinary và lưu kết quả.
        // Các lần sau, nó sẽ lấy thẳng từ cache mà không cần gọi API.
        return Cache::rememberForever($cacheKey, function () use ($path) {
            return cloudinary_url($path);
        });
    }
}