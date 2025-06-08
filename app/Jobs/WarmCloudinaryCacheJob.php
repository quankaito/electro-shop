<?php

namespace App\Jobs;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Post;
use App\Models\ProductImage;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Thêm dòng này
use Illuminate\Support\Facades\Cache;   // Thêm dòng này

class WarmCloudinaryCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        Log::info('Cloudinary cache warming job started...');

        $warmUp = function ($item, $field) {
            $path = $item->{$field};
            if (!empty($path)) {
                $cacheKey = 'cloudinary.url.' . md5($path);
                // Lấy URL thật từ Cloudinary
                $url = Storage::disk('cloudinary')->url($path);
                // Lưu vào cache vĩnh viễn
                Cache::forever($cacheKey, $url);
            }
        };

        Banner::query()->get()->each(function ($item) use ($warmUp) {
            $warmUp($item, 'image_url_desktop');
            $warmUp($item, 'image_url_mobile');
        });

        Brand::query()->get()->each(fn ($item) => $warmUp($item, 'logo'));
        Category::query()->get()->each(fn ($item) => $warmUp($item, 'image'));
        PaymentMethod::query()->get()->each(fn ($item) => $warmUp($item, 'logo'));
        Post::query()->get()->each(fn ($item) => $warmUp($item, 'featured_image'));
        ProductImage::query()->get()->each(fn ($item) => $warmUp($item, 'image_path'));
        ShippingMethod::query()->get()->each(fn ($item) => $warmUp($item, 'logo'));
        User::query()->get()->each(fn ($item) => $warmUp($item, 'avatar'));

        Log::info('Cloudinary cache warming job completed successfully!');
    }
}