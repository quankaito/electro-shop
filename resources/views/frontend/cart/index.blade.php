@extends('layouts.app')

@section('title', 'Giỏ Hàng Của Bạn')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-center mb-8">Giỏ Hàng</h1>

    {{-- Component Livewire quản lý toàn bộ hiển thị và logic --}}
    @livewire('frontend.cart.shopping-cart-page')

</div>
@endsection
