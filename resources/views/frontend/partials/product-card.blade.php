{{-- resources/views/frontend/partials/product-card.blade.php --}}
<div class="group relative bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
    {{-- Link tới trang chi tiết sản phẩm --}}
    <a href="{{ route('products.show', $product->slug) }}">
        {{-- Ảnh thumbnail --}}
        <div class="w-full h-56 bg-gray-100 flex items-center justify-center">
            @php
                // Lấy ảnh thumbnail (quan hệ thumbnail()), fallback ảnh đầu tiên nếu không có
                $thumb   = $product->thumbnail;
                $imgPath = $thumb ? $thumb->image_path : ($product->images->first()?->image_path);
            @endphp

            @if($imgPath)
                <img
                    src="{{ cloudinary_url($imgPath) }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover"
                >
            @else
                <span class="text-gray-400">Chưa có ảnh</span>
            @endif

            {{-- Hiển thị badge giảm giá nếu có sale --}}
            @if($product->sale_price && $product->sale_price < $product->regular_price)
                @php
                    $discountPercentage = round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100);
                @endphp
                <span
                    class="absolute top-2 right-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded"
                >
                    -{{ $discountPercentage }}%
                </span>
            @endif
        </div>
    </a>

    {{-- Phần thông tin & nút --}}
    <div class="p-4">
        {{-- Tên category --}}
        @if($product->category)
            <p class="text-xs text-gray-500 mb-1">{{ $product->category->name }}</p>
        @endif

        {{-- Tên sản phẩm --}}
        <a href="{{ route('products.show', $product->slug) }}">
            <h3 class="text-md font-semibold text-gray-800 mb-2 truncate group-hover:text-indigo-600 transition-colors">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Giá --}}
        <div class="flex items-center justify-between mb-1">
            @if($product->sale_price && $product->sale_price < $product->regular_price)
                <div>
                    <span class="text-lg font-bold text-red-600">
                        {{ number_format($product->sale_price, 0, ',', '.') }} VNĐ
                    </span>
                    <span class="text-sm text-gray-400 line-through ml-1">
                        {{ number_format($product->regular_price, 0, ',', '.') }} VNĐ
                    </span>
                </div>
            @else
                <span class="text-lg font-bold text-indigo-600">
                    {{ number_format($product->regular_price, 0, ',', '.') }} VNĐ
                </span>
            @endif

            {{-- Nút Wishlist (Livewire) --}}
            @livewire(
                'frontend.wishlist-button',
                ['productId' => $product->id],
                key('card-wishlist-'.$product->id)
            )
        </div>

        {{-- Thông tin tồn kho --}}
        @if($product->stock_quantity > 0)
            <p class="text-sm text-green-600 mb-2">Còn {{ $product->stock_quantity }} sản phẩm</p>
        @else
            <p class="text-sm text-red-600 mb-2">Hết hàng</p>
        @endif

        {{-- Nút Add to Cart (Livewire) --}}
        <div class="pt-2">
            @if($product->variants->isNotEmpty())
                {{-- Nếu có variant, hiển thị nút riêng cho từng variant --}}
                @foreach($product->variants as $variant)
                    <div class="mb-2 flex justify-between items-center p-2 border rounded">
                        <div class="text-sm">
                            @foreach($variant->options as $opt)
                                {{ $opt->attribute->name }}: {{ $opt->value }}
                                @if(!$loop->last), @endif
                            @endforeach
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-indigo-600">
                                {{ number_format($variant->specific_price, 0, ',', '.') }}₫
                            </span>
                            @livewire(
                                'frontend.product.add-to-cart-button',
                                ['product' => $product, 'variant' => $variant],
                                key('variant-card-cart-'.$variant->id)
                            )
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Không có variant --}}
                @livewire(
                    'frontend.product.add-to-cart-button',
                    ['product' => $product],
                    key('card-cart-'.$product->id)
                )
            @endif
        </div>
    </div>
</div>
