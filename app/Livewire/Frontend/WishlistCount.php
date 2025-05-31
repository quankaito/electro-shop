<?php

namespace App\Livewire\Frontend;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WishlistCount extends Component
{
    public $count = 0;

    protected $listeners = ['wishlistUpdated' => 'updateCount'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        if (Auth::check()) {
            $this->count = Auth::user()->wishlistProducts()->count();
        } else {
            $this->count = 0;
        }
    }

    public function render()
    {
        return view('livewire.frontend.wishlist-count');
    }
}