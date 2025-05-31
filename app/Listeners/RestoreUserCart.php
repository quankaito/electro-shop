<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Gloudemans\Shoppingcart\Facades\Cart;

class RestoreUserCart
{
    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        // $event->user chính là user vừa login thành công
        if ($event->user && $event->user->id) {
            // Khôi phục giỏ hàng đã lưu (nếu có) cho user này
            Cart::instance('default')->restore($event->user->id);
        }
    }
}
