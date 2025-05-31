@extends('layouts.app')

@section('title', 'Sản Phẩm Yêu Thích')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        @include('frontend.account.partials.sidebar')

        <main class="w-full md:w-3/4">
            <h1 class="text-2xl font-semibold mb-6">Sản Phẩm Yêu Thích</h1>

            @if($wishlistItems->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($wishlistItems as $product)
                        @include('frontend.partials.product-card', ['product' => $product])
                        {{-- Lưu ý: product-card có thể cần điều chỉnh nút wishlist/add-to-cart
                             để phù hợp với ngữ cảnh trang wishlist (ví dụ: nút "Xóa khỏi yêu thích") --}}
                    @endforeach
                </div>
                <div class="mt-8">
                    {{ $wishlistItems->links() }}
                </div>
            @else
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <p class="text-gray-600">Danh sách yêu thích của bạn hiện đang trống.</p>
                    <a href="{{ route('products.index') }}" class="mt-4 inline-block text-indigo-600 hover:underline">Khám phá sản phẩm ngay!</a>
                </div>
            @endif
        </main>
    </div>
</div>
@endsection