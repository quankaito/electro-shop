<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Gloudemans\Shoppingcart\Facades\Cart;

class StoreUserCart
{
    /**
     * Handle the event.
     *
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        // $event->user chính là user vừa logout
        if ($event->user && $event->user->id) {
            // Lưu giỏ hàng hiện tại vào database, gắn với user này
            Cart::instance('default')->store($event->user->id);
        }
    }
}
