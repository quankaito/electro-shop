<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

if (! function_exists('get_cloudinary_urls')) {
    /**
     * Lấy URL cho một MẢNG các đường dẫn, tối ưu hóa việc đọc và ghi cache.
     *
     * @param Illuminate\Support\Collection|array $paths
     * @return array
     */
    function get_cloudinary_urls($paths): array
    {
        $uniquePaths = collect($paths)->filter()->unique();
        if ($uniquePaths->isEmpty()) {
            return [];
        }

        $cacheKeys = $uniquePaths->mapWithKeys(fn ($path) => [$path => 'cloudinary.url.' . md5($path)]);

        // Lấy tất cả URL đã có trong cache trong một lần gọi
        $cachedUrls = Cache::many($cacheKeys->values()->toArray());
        $cachedUrls = array_filter($cachedUrls);

        $finalUrls = [];
        $pathsToFetch = [];

        // Phân loại: URL nào đã có, path nào cần fetch
        foreach ($cacheKeys as $path => $cacheKey) {
            if (isset($cachedUrls[$cacheKey])) {
                $finalUrls[$path] = $cachedUrls[$cacheKey];
            } else {
                $pathsToFetch[] = $path;
            }
        }

        // Nếu có path cần fetch, xử lý chúng
        if (!empty($pathsToFetch)) {
            foreach ($pathsToFetch as $path) {
                try {
                    // Gọi API cho từng path còn thiếu và ghi vào cache
                    $url = Storage::disk('cloudinary')->url($path);
                    $finalUrls[$path] = $url;
                    Cache::forever('cloudinary.url.' . md5($path), $url);
                } catch (\Exception $e) {
                    Log::error('Cloudinary URL Fetch Failed', ['path' => $path, 'error' => $e->getMessage()]);
                    $finalUrls[$path] = ''; // Trả về rỗng nếu có lỗi
                }
            }
        }

        return $finalUrls;
    }
}

if (! function_exists('cloudinary_url')) {
    /**
     * Hàm helper cho một URL duy nhất, bây giờ nó sẽ gọi hàm batch.
     */
    function cloudinary_url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }
        // Gọi hàm batch với chỉ một item
        return get_cloudinary_urls([$path])[$path] ?? '';
    }
}