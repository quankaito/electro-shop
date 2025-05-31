<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = Auth::user();
        return view('frontend.account.dashboard', compact('user')); // Có thể nhúng Livewire components
    }

    public function profile()
    {
        return view('frontend.account.profile'); // Sẽ chứa <livewire:user-profile-form />
    }

    public function orders()
    {
        $orders = Auth::user()->orders()->latest()->paginate(10);
        return view('frontend.account.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        // Đảm bảo đơn hàng thuộc về user đang đăng nhập
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load('items.product', 'items.variant', 'shippingAddress', 'billingAddress', 'paymentMethod', 'shippingMethod');
        return view('frontend.account.order-detail', compact('order'));
    }

    public function addresses()
    {
        return view('frontend.account.addresses'); // Sẽ chứa <livewire:user-address-manager />
    }

    // Change password có thể dùng controller hoặc Livewire
    public function showChangePasswordForm()
    {
        return view('frontend.account.change-password');
    }

    public function handleChangePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
    public function wishlist()
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlistProducts()->with(['images', 'category'])->paginate(10); // Eager load
        return view('frontend.account.wishlist', compact('user', 'wishlistItems'));
    }
}