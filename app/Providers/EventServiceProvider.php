<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\RestoreUserCart;
use App\Listeners\StoreUserCart;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
    }
}
