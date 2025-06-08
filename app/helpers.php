<?php

use Illuminate\Support\Facades\Cache;

if (! function_exists('cloudinary_url')) {
    /**
     * Lấy URL từ CACHE. Nếu không có, trả về chuỗi rỗng để tránh timeout.
     * Công việc điền vào cache thuộc về một Job chạy nền.
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

        $cacheKey = 'cloudinary.url.' . md5($path);

        // Chỉ LẤY từ cache. Nếu không có, trả về giá trị mặc định là chuỗi rỗng ''.
        // KHÔNG dùng rememberForever ở đây nữa.
        return Cache::get($cacheKey, '');
    }
}