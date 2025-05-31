<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Services\CartService;

class CheckoutController extends Controller
{
    // protected $cartService;

    // public function __construct(CartService $cartService)
    // {
    //     $this->middleware('auth'); // Bắt buộc đăng nhập để checkout
    //     $this->cartService = $cartService;
    // }

    public function index()
    {
        // if ($this->cartService->isEmpty()) {
        //     return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        // }
        // Trang checkout sẽ là một Livewire component toàn trang
        return view('frontend.checkout.index'); // View này sẽ chứa <livewire:checkout-page />
    }

    public function success(Request $request, $orderId) // $orderId có thể lấy từ session hoặc query
    {
        // Xóa giỏ hàng sau khi thanh toán thành công (trong Livewire component hoặc ở đây)
        // $this->cartService->clear();
        // $order = Order::findOrFail($orderId); // Hoặc Order::where('order_number', $orderId)->firstOrFail();
        // return view('frontend.checkout.success', compact('order'));
        return view('frontend.checkout.success', ['orderNumber' => session('last_order_number')]);
    }
}