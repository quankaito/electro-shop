<?php

namespace App\Livewire\Frontend\Cart;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;

class ShoppingCartPage extends Component
{
    // Giá trị mặc định
    public $cartItems    = [];
    public $cartSubtotal = 0;
    public $cartTax      = 0;
    public $cartTotal    = 0;

    protected $listeners = ['cartUpdated' => 'updateCartContent'];

    public function mount()
    {
        $this->updateCartContent();
    }

    public function updateCartContent()
    {
        $items = Cart::content();

        $this->cartItems = $items->map(function ($item) {
            return [
                'rowId'   => $item->rowId,
                'id'      => $item->id,
                'name'    => $item->name,
                'qty'     => $item->qty,
                'price'   => $item->price,
                'total'   => $item->price * $item->qty,
                'options' => $item->options->toArray(),
            ];
        })->toArray();

        $this->cartSubtotal = Cart::subtotal(2, '.', '');
        $this->cartTax      = Cart::tax(2, '.', '');
        $this->cartTotal    = Cart::total(2, '.', '');
    }

    public function updateQuantity($rowId, $newQuantity)
    {
        $qty = max(1, (int) $newQuantity);
        Cart::update($rowId, $qty);
        $this->updateCartContent();
        $this->dispatch('cartUpdated');
    }

    public function incrementQuantity($rowId)
    {
        $item = Cart::get($rowId);
        Cart::update($rowId, $item->qty + 1);
        $this->updateCartContent();
        $this->dispatch('cartUpdated');
    }

    public function decrementQuantity($rowId)
    {
        $item = Cart::get($rowId);
        if ($item->qty > 1) {
            Cart::update($rowId, $item->qty - 1);
            $this->updateCartContent();
            $this->dispatch('cartUpdated');
        }
    }

    public function removeItem($rowId)
    {
        Cart::remove($rowId);
        $this->updateCartContent();
        $this->dispatch('cartUpdated');
        $this->dispatch('showToast', message: 'Item removed from cart.', type: 'info');
    }

    public function clearCart()
    {
        Cart::destroy();
        $this->updateCartContent();
        $this->dispatch('cartUpdated');
        $this->dispatch('showToast', message: 'Cart cleared.', type: 'info');
    }

    public function render()
    {
        return view('livewire.frontend.cart.shopping-cart-page', [
            'cartItems'    => $this->cartItems,
            'cartSubtotal' => $this->cartSubtotal,
            'cartTax'      => $this->cartTax,
            'cartTotal'    => $this->cartTotal,
            'isEmpty'      => count($this->cartItems) === 0, // Dùng để kiểm tra giỏ trống
        ])->layout('layouts.app');
    }
}
