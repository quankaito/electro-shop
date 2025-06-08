{{-- resources/views/frontend/products/show.blade.php --}}
@extends('layouts.app')

@section('title', $product->name)

@push('styles')
    {{-- Nếu cần thêm CSS cho gallery (nếu dùng thư viện JS), đặt ở đây --}}
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    <nav class="mb-6 text-sm" aria-label="Breadcrumb">
        <ol class="list-none p-0 inline-flex">
            <li class="flex items-center">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-indigo-600">Trang chủ</a>
                <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                    <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/>
                </svg>
            </li>
            <li class="flex items-center">
                <a href="{{ route('products.category', $product->category->slug) }}"
                   class="text-gray-500 hover:text-indigo-600">
                    {{ $product->category->name }}
                </a>
                <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                    <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/>
                </svg>
            </li>
            <li class="flex items-center">
                <span class="text-gray-700">{{ $product->name }}</span>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div>
            @if($product->images->isNotEmpty())
                {{-- BƯỚC MỚI: Lấy tất cả URL trong một lần gọi duy nhất ở đầu --}}
                @php
                    $imagePaths = $product->images->pluck('image_path')->all();
                    $imageUrls = get_cloudinary_urls($imagePaths);
                @endphp

                {{-- Ảnh đại diện lớn --}}
                <div class="mb-4">
                    @php
                        $firstImagePath = $product->images->firstWhere('is_thumbnail', true)
                            ? $product->images->firstWhere('is_thumbnail', true)->image_path
                            : $product->images->first()->image_path;
                    @endphp
                    <img
                        id="mainProductImage"
                        src="{{ $imageUrls[$firstImagePath] ?? '' }}" {{-- Sử dụng mảng đã lấy --}}
                        alt="{{ $product->name }}"
                        class="w-full h-auto max-h-[500px] object-contain rounded-lg border"
                    >
                </div>

                {{-- Dòng thumbnails để click thay đổi ảnh --}}
                @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->images as $image)
                            <img
                                src="{{ $imageUrls[$image->image_path] ?? '' }}" {{-- Sử dụng mảng đã lấy --}}
                                alt="{{ $image->alt_text ?: $product->name }}"
                                class="w-full h-24 object-cover rounded cursor-pointer border hover:border-indigo-500"
                                onclick="document.getElementById('mainProductImage').src='{{ $imageUrls[$image->image_path] ?? '' }}'"
                            >
                        @endforeach
                    </div>
                @endif
            @else
                {{-- Fallback nếu không có ảnh --}}
                <img src="https://via.placeholder.com/500x500?text=No+Image" ... >
            @endif
        </div>

        <!-- Product Details -->
        <div>
            <h1 class="text-3xl lg:text-4xl font-bold mb-2">{{ $product->name }}</h1>

            @if($product->brand)
                <p class="text-sm text-gray-500 mb-3">
                    Thương hiệu:
                    <a href="{{ route('products.index', ['brand' => $product->brand->slug]) }}"
                       class="text-indigo-600 hover:underline">
                        {{ $product->brand->name }}
                    </a>
                </p>
            @endif

            <p class="text-sm text-gray-500 mb-4">
                SKU: {{ $product->sku ?: 'N/A' }} |
                Lượt xem: {{ $product->views_count }}
            </p>

            <div class="mb-4">
                @if($product->sale_price && $product->sale_price < $product->regular_price)
                    <span class="text-3xl font-bold text-red-600">
                        {{ number_format($product->sale_price, 0, ',', '.') }} VNĐ
                    </span>
                    <span class="text-xl text-gray-500 line-through ml-2">
                        {{ number_format($product->regular_price, 0, ',', '.') }} VNĐ
                    </span>
                    @php
                        $discountPercentage = round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100);
                    @endphp
                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-600 text-xs font-semibold rounded">
                        -{{ $discountPercentage }}%
                    </span>
                @else
                    <span class="text-3xl font-bold text-indigo-600">
                        {{ number_format($product->regular_price, 0, ',', '.') }} VNĐ
                    </span>
                @endif
            </div>

            <div class="mb-4 text-sm">
                {!! $product->short_description !!}
            </div>

            {{-- Nếu có variants --}}
            @if($product->variants->isNotEmpty())
                <div class="mb-6">
                    <h3 class="text-md font-semibold mb-2">Lựa chọn phiên bản:</h3>
                    <div class="space-y-2">
                    @foreach($product->variants as $variant)
                        <div
                            class="p-2 border rounded flex justify-between items-center"
                        >
                            <span>
                                @foreach($variant->options as $optionValue)
                                    {{ $optionValue->attribute->name }}: {{ $optionValue->value }}
                                    @if(!$loop->last), @endif
                                @endforeach
                            </span>
                            <span>
                                {{ number_format($variant->specific_price, 0, ',', '.') }} VNĐ
                            </span>
                            {{-- Nút Add to Cart riêng cho từng variant (Livewire) --}}
                            @livewire(
                                'frontend.product.add-to-cart-button',
                                ['product' => $product, 'variant' => $variant],
                                key('variant-cart-'.$variant->id)
                            )
                        </div>
                    @endforeach
                    </div>
                </div>
            @else
                {{-- Nếu chỉ có 1 sản phẩm (không có variant) --}}
                <div class="my-6">
                    @livewire(
                        'frontend.product.add-to-cart-button',
                        ['product' => $product],
                        key('product-cart-'.$product->id)
                    )
                </div>
            @endif

            {{-- Nút wishlist --}}
            <div class="mt-4">
                @livewire(
                    'frontend.wishlist-button',
                    ['productId' => $product->id],
                    key('wishlist-'.$product->id)
                )
            </div>

            <div class="mt-6 border-t pt-4">
                <p class="text-sm text-gray-600"><strong>Tình trạng:</strong>
                    @if($product->stock_status == 'in_stock' && $product->stock_quantity > 0)
                        <span class="text-green-600">Còn hàng ({{ $product->stock_quantity }})</span>
                    @elseif($product->stock_status == 'on_backorder')
                        <span class="text-yellow-600">Đặt trước</span>
                    @else
                        <span class="text-red-600">Hết hàng</span>
                    @endif
                </p>
                <p class="text-sm text-gray-600">
                    <strong>Danh mục:</strong>
                    <a href="{{ route('products.category', $product->category->slug) }}"
                       class="text-indigo-600 hover:underline">
                        {{ $product->category->name }}
                    </a>
                </p>
                {{-- Thêm tags, thông tin bổ sung nếu có --}}
            </div>
        </div>
    </div>

    <!-- Tab: Description / Specifications / Reviews -->
    <div class="mt-12" x-data="{ activeTab: 'description' }">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button
                    @click="activeTab = 'description'"
                    :class="activeTab === 'description'
                        ? 'border-indigo-500 text-indigo-600' 
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none"
                >
                    Mô Tả Sản Phẩm
                </button>
                <button
                    @click="activeTab = 'specifications'"
                    :class="activeTab === 'specifications'
                        ? 'border-indigo-500 text-indigo-600' 
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none"
                >
                    Thông Số Kỹ Thuật
                </button>
                <button
                    @click="activeTab = 'reviews'"
                    :class="activeTab === 'reviews'
                        ? 'border-indigo-500 text-indigo-600' 
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none"
                >
                    Đánh Giá ({{ $product->reviews->where('is_approved', true)->count() }})
                </button>
            </nav>
        </div>

        <div class="mt-6">
            <div x-show="activeTab === 'description'" class="prose lg:prose-lg max-w-none">
                {!! $product->description !!}
            </div>
            <div x-show="activeTab === 'specifications'" class="prose lg:prose-lg max-w-none">
                {{-- Hiển thị bảng thông số kỹ thuật (nếu bạn đã chuẩn bị dữ liệu) --}}
                @if($product->specifications)
                    <table class="w-full text-sm text-left">
                        <tbody>
                        @foreach($product->specifications as $label => $value)
                            <tr class="border-b">
                                <td class="py-2 font-semibold">{{ $label }}</td>
                                <td class="py-2">{{ $value }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-600">Chưa có thông số kỹ thuật.</p>
                @endif
            </div>
            <div x-show="activeTab === 'reviews'">
                {{-- Livewire component để hiển thị form & list review --}}
                @livewire(
                    'frontend.product.product-review',
                    ['product' => $product],
                    key('reviews-for-'.$product->id)
                )
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <section class="mt-16">
        <h2 class="text-2xl font-semibold mb-6">Sản Phẩm Liên Quan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
                @include('frontend.partials.product-card', ['product' => $relatedProduct])
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
