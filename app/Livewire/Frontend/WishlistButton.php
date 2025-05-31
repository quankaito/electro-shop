<?php

namespace App\Livewire\Frontend;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WishlistButton extends Component
{
    public $productId;
    public $isWishlisted = false;

    public function mount($productId)
    {
        $this->productId = $productId;
        if (Auth::check()) {
            $this->isWishlisted = Auth::user()->wishlistProducts()->where('product_id', $this->productId)->exists();
        }
    }

    public function toggleWishlist()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if ($this->isWishlisted) {
            $user->wishlistProducts()->detach($this->productId);
            $this->isWishlisted = false;
            $this->dispatch('showToast', message: 'Removed from wishlist.', type: 'info');
        } else {
            $user->wishlistProducts()->attach($this->productId);
            $this->isWishlisted = true;
            $this->dispatch('showToast', message: 'Added to wishlist!', type: 'success');
        }
        $this->dispatch('wishlistUpdated'); // Event cho wishlist count ở header
    }

    public function render()
    {
        return view('livewire.frontend.wishlist-button');
    }
}