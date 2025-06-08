<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CloudinaryCacheObserver
{
    // Chạy khi một bản ghi được TẠO MỚI
    public function created(Model $model): void
    {
        $this->updateCacheForModel($model);
    }

    // Chạy khi một bản ghi được CẬP NHẬT
    public function updated(Model $model): void
    {
        $this->updateCacheForModel($model);
    }

    /**
     * Hàm trung tâm để cập nhật cache cho một model cụ thể.
     */
    private function updateCacheForModel(Model $model): void
    {
        $imageFields = $this->getImageFieldsForModel($model);

        foreach ($imageFields as $field) {
            // Chỉ cập nhật nếu trường đó có giá trị
            if (!empty($model->{$field})) {
                $this->warmCache($model->{$field});
            }
        }
    }

    /**
     * Lấy và lưu URL của một đường dẫn ảnh vào cache.
     */
    private function warmCache(string $path): void
    {
        try {
            $cacheKey = 'cloudinary.url.' . md5($path);
            $url = Storage::disk('cloudinary')->url($path);
            Cache::forever($cacheKey, $url);
        } catch (\Exception $e) {
            // Ghi lại log nếu có lỗi khi lấy URL từ Cloudinary
            Log::error('Failed to warm Cloudinary cache for path: ' . $path, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Trả về danh sách các trường chứa ảnh cho từng loại model.
     * @return string[]
     */
    private function getImageFieldsForModel(Model $model): array
    {
        return match (get_class($model)) {
            \App\Models\Banner::class => ['image_url_desktop', 'image_url_mobile'],
            \App\Models\Brand::class => ['logo'],
            \App\Models\Category::class => ['image'],
            \App\Models\PaymentMethod::class => ['logo'],
            \App\Models\Post::class => ['featured_image'],
            \App\Models\ProductImage::class => ['image_path'],
            \App\Models\ShippingMethod::class => ['logo'],
            \App\Models\User::class => ['avatar'],
            default => [],
        };
    }
}