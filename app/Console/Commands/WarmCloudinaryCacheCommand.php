<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Post;
use App\Models\ProductImage;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Console\Command;

class WarmCloudinaryCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:warm-cloudinary-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches and caches all Cloudinary URLs to prevent rate limiting and timeouts on deploy.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Cloudinary cache warming...');

        // 1. Cache Banners
        $this->line('Caching Banners...');
        Banner::query()->whereNotNull('image_url_desktop')->orWhereNotNull('image_url_mobile')->get()->each(function ($item) {
            cloudinary_url($item->image_url_desktop);
            cloudinary_url($item->image_url_mobile);
        });

        // 2. Cache Brands
        $this->line('Caching Brands...');
        Brand::query()->whereNotNull('logo')->get()->each(fn ($item) => cloudinary_url($item->logo));

        // 3. Cache Categories
        $this->line('Caching Categories...');
        Category::query()->whereNotNull('image')->get()->each(fn ($item) => cloudinary_url($item->image));

        // 4. Cache Payment Methods
        $this->line('Caching Payment Methods...');
        PaymentMethod::query()->whereNotNull('logo')->get()->each(fn ($item) => cloudinary_url($item->logo));

        // 5. Cache Posts
        $this->line('Caching Posts...');
        Post::query()->whereNotNull('featured_image')->get()->each(fn ($item) => cloudinary_url($item->featured_image));

        // 6. Cache Product Images
        $this->line('Caching Product Images...');
        ProductImage::query()->whereNotNull('image_path')->get()->each(fn ($item) => cloudinary_url($item->image_path));

        // 7. Cache Shipping Methods
        $this->line('Caching Shipping Methods...');
        ShippingMethod::query()->whereNotNull('logo')->get()->each(fn ($item) => cloudinary_url($item->logo));

        // 8. Cache User Avatars
        $this->line('Caching User Avatars...');
        User::query()->whereNotNull('avatar')->get()->each(fn ($item) => cloudinary_url($item->avatar));

        $this->info('Cloudinary cache warming completed successfully!');
        
        return self::SUCCESS;
    }
}