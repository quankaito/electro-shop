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

        Banner::query()->whereNotNull('image_url_desktop')->orWhereNotNull('image_url_mobile')->get()->each(function ($item) {
            cloudinary_url($item->image_url_desktop);
            cloudinary_url($item->image_url_mobile);
        });

        Brand::query()->whereNotNull('logo')->get()->each(fn ($item) => cloudinary_url($item->logo));
        Category::query()->whereNotNull('image')->get()->each(fn ($item) => cloudinary_url($item->image));
        PaymentMethod::query()->whereNotNull('logo')->get()->each(fn ($item) => cloudinary_url($item->logo));
        Post::query()->whereNotNull('featured_image')->get()->each(fn ($item) => cloudinary_url($item->featured_image));
        ProductImage::query()->whereNotNull('image_path')->get()->each(fn ($item) => cloudinary_url($item->image_path));
        ShippingMethod::query()->whereNotNull('logo')->get()->each(fn ($item) => cloudinary_url($item->logo));
        User::query()->whereNotNull('avatar')->get()->each(fn ($item) => cloudinary_url($item->avatar));

        Log::info('Cloudinary cache warming job completed successfully!');
    }
}