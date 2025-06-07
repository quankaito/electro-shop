<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\BlogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Livewire\User\Dashboard as UserDashboard; 
use App\Http\Controllers\Api\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Các route cho trang tĩnh và trang chủ
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'handleContactForm'])->name('contact.submit');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/about-us', [PageController::class, 'about'])->name('about');
// Thêm các trang tĩnh khác nếu cần (ví dụ: privacy, terms)

// Các route cho sản phẩm
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('products.show'); // Sử dụng slug
Route::get('/category/{category:slug}', [ProductController::class, 'showByCategory'])->name('products.category'); // Sử dụng slug

// Các route cho giỏ hàng
// Trang giỏ hàng chính sẽ được quản lý bởi Livewire component
Route::get('/cart', [CartController::class, 'index'])->name('cart.index')->middleware('auth');
// Các actions của giỏ hàng (add, update, remove) sẽ được xử lý bởi Livewire components

// Các route cho thanh toán
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index')->middleware('auth'); // Cần đăng nhập để checkout
Route::get('/checkout/success/{orderId?}', [CheckoutController::class, 'success'])->name('checkout.success')->middleware('auth');
// Xử lý đặt hàng (place order) sẽ được thực hiện bởi Livewire component CheckoutPage

// Các route cho blog/tin tức
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show'); // Sử dụng slug

// Các route cho tài khoản người dùng (yêu cầu đăng nhập)
Route::middleware(['auth'])->prefix('account')->name('account.')->group(function () {
    Route::get('/dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    // Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update'); // Được xử lý bởi Livewire UserProfileForm
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AccountController::class, 'orderDetail'])->name('orders.detail');
    Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
    // Thêm, sửa, xóa địa chỉ được xử lý bởi Livewire UserAddressManager

    Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
    // Thêm/xóa wishlist được xử lý bởi Livewire WishlistButton

    Route::get('/change-password', [AccountController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/change-password', [AccountController::class, 'handleChangePassword'])->name('password.update');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Các route mặc định của Laravel Breeze/Jetstream (nếu bạn dùng)
// Route::view('dashboard', 'dashboard') // Cái này có thể được thay thế bằng AccountController@dashboard
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard'); // Đã có account.dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    // Route này sẽ gọi trực tiếp Livewire component
    Route::get('dashboard', UserDashboard::class)
         ->name('dashboard');
});

Route::view('profile', 'profile') // Cái này là trang profile mặc định của Breeze/Jetstream, có thể bạn muốn dùng AccountController@profile
    ->middleware(['auth'])
    ->name('profile'); // Đã có account.profile

// Thư mục auth.php được require ở đây
require __DIR__.'/auth.php';

// Fallback route (trang 404 tùy chỉnh nếu muốn)
// Route::fallback(function () {
//    return view('errors.404'); // Tạo view errors/404.blade.php
// });
Route::middleware('auth')->group(function () {
    Route::post('/api/chat/message', [ChatController::class, 'sendMessage'])->name('chat.send.message');
});
// --- ROUTE CHẨN ĐOÁN (CÓ THỂ XÓA SAU KHI SỬA LỖI) ---
// Route::get('/debug-livewire-url', function () {
//     echo '<h1>Chẩn đoán URL cho Livewire/Filament</h1>';

//     echo '<h2>Cấu hình từ file .env (config)</h2>';
//     echo '<b>config("app.url"):</b> ' . config('app.url') . '<br>';
//     echo '<b>config("livewire.app_url"):</b> ' . config('livewire.app_url') . '<br>';
//     echo '<b>config("session.domain"):</b> ' . var_export(config('session.domain'), true) . '<br>';

//     echo '<h2>URL được tạo ra bởi Laravel</h2>';
//     echo '<b>url("/"):</b> ' . url('/') . '<br>';

//     echo '<h2>Thông tin Request thực tế</h2>';
//     echo '<b>Request::getSchemeAndHttpHost():</b> ' . request()->getSchemeAndHttpHost() . '<br>';
//     echo '<b>Request::isSecure():</b> ' . (request()->isSecure() ? 'true' : 'false') . '<br>';
// });