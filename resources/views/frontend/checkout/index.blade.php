@extends('layouts.app')

@section('title', 'Thanh Toán Đơn Hàng')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-center mb-8">Thanh Toán</h1>

    {{-- Đây là nơi component Livewire CheckoutPage sẽ được nhúng --}}
    {{-- @livewire('frontend.checkout.checkout-page') --}}
    @livewire('frontend.checkout.checkout-page')
    <!-- <div class="p-10 border-2 border-dashed border-gray-300 rounded-lg">
        <h2 class="text-xl font-semibold text-center text-gray-500 mb-4">Đang tải trang thanh toán...</h2>
        <p class="text-center text-gray-500">Component Livewire "CheckoutPage" sẽ quản lý toàn bộ quy trình thanh toán, bao gồm thông tin giao hàng, phương thức vận chuyển, phương thức thanh toán, mã giảm giá và xác nhận đơn hàng.</p>
    </div> -->
</div>
@endsection