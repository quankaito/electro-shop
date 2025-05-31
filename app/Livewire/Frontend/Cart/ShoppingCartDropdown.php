<?php

namespace App\Livewire\Frontend\Cart;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;

class ShoppingCartDropdown extends Component
{
    public $cartItems;
    public $cartSubtotal;   // Tạm tính (subtotal) chưa bao gồm thuế
    public $cartTax;        // Thuế
    public $cartTotal;      // Tổng (subtotal + thuế)
    public $cartCount;

    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
    }

    public function updateCart()
    {
        // Lấy danh sách sản phẩm trong giỏ
        $this->cartItems = Cart::content()->map(function ($item) {
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

        // Cart::subtotal() trả về chuỗi đã format (có thể có dấu phẩy),
        // nên chúng ta ép thành float để dễ tính toán hoặc format trong view
        $rawSubtotal       = (string) Cart::subtotal(2, '.', ''); // Ví dụ: "100000.00"
        $this->cartSubtotal = (float) str_replace(',', '', $rawSubtotal);

        // Tương tự với tax
        $rawTax = (string) Cart::tax(2, '.', ''); // Ví dụ: "10000.00"
        $this->cartTax = (float) str_replace(',', '', $rawTax);

        // Cart::total() là subtotal + tax (nếu bạn cấu hình thế)
        $rawTotal     = (string) Cart::total(2, '.', ''); // Ví dụ: "110000.00"
        $this->cartTotal = (float) str_replace(',', '', $rawTotal);

        // Số lượng item trong giỏ
        $this->cartCount = Cart::count();
    }

    public function removeItem($rowId)
    {
        Cart::remove($rowId);
        $this->updateCart();
        $this->dispatch('cartUpdated'); // Sửa lại: emit thay vì dispatch, giúp Livewire bắt đúng sự kiện
        $this->dispatch('showToast', message: 'Item removed from cart.', type: 'info');
    }

    public function render()
    {
        return view('livewire.frontend.cart.shopping-cart-dropdown');
    }
}
