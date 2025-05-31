<?php

namespace App\Livewire\Frontend\Product;

use Livewire\Component;
// use App\Services\CartService; // Hoặc package cart
use Gloudemans\Shoppingcart\Facades\Cart; // Ví dụ dùng bumbummen99/shoppingcart

class AddToCartButton extends Component
{
    public $productId;
    public $productName;
    public $productPrice;
    public $quantity = 1;
    public $variantId;
    public $options = []; // ['size' => 'XL', 'color' => 'Red']

    public function mount($product, $variant = null)
    {
        $this->productId = $product->id;
        $this->productName = $product->name;
        $this->productPrice = $variant ? $variant->specific_price : ($product->sale_price ?: $product->regular_price);
        if ($variant) {
            $this->variantId = $variant->id;
            // Lấy options từ variant
            foreach ($variant->options as $optionValue) {
                $this->options[$optionValue->attribute->name] = $optionValue->value;
            }
        }
    }

    public function addToCart()
    {
        Cart::add([
            'id' => $this->variantId ?: $this->productId, // ID duy nhất cho item trong giỏ
            'name' => $this->productName,
            'qty' => $this->quantity,
            'price' => $this->productPrice,
            'weight' => 0, // Optional
            'options' => array_merge($this->options, ['product_id_original' => $this->productId]) // Lưu thêm product_id gốc
        ]);

        $this->dispatch('cartUpdated'); // Gửi event để các component khác (vd: cart dropdown) cập nhật
        $this->dispatch('showToast', message: 'Added to cart successfully!', type: 'success');
    }

    public function render()
    {
        return view('livewire.frontend.product.add-to-cart-button');
    }
}