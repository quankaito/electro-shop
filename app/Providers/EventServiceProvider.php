<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\RestoreUserCart;
use App\Listeners\StoreUserCart;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Post;
use App\Models\ProductImage;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Observers\CloudinaryCacheObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Khi user login thành công:
        Login::class => [
            RestoreUserCart::class,
        ],

        // Khi user logout:
        Logout::class => [
            StoreUserCart::class,
        ],

        // (Nếu bạn có thêm listener khác, có thể để ở đây)
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        // === ĐĂNG KÝ OBSERVER CHO CÁC MODEL CÓ ẢNH ===
        Banner::observe(CloudinaryCacheObserver::class);
        Brand::observe(CloudinaryCacheObserver::class);
        Category::observe(CloudinaryCacheObserver::class);
        PaymentMethod::observe(CloudinaryCacheObserver::class);
        Post::observe(CloudinaryCacheObserver::class);
        ProductImage::observe(CloudinaryCacheObserver::class);
        ShippingMethod::observe(CloudinaryCacheObserver::class);
        User::observe(CloudinaryCacheObserver::class);
    }
}
