{{-- resources/views/livewire/frontend/cart/shopping-cart-page.blade.php --}}
<div>
    @if(count($cartItems) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">
                    Sản phẩm trong giỏ ({{ count($cartItems) }})
                </h2>
                <div class="divide-y divide-gray-200">
                    @foreach ($cartItems as $item)
                        <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between"
                             wire:key="cart-page-{{ $item['rowId'] }}">
                            <div class="flex items-start mb-4 sm:mb-0">
                                @php
                                    $options = $item['options'] ?? [];
                                    $prodId  = $options['product_id_original'] ?? null;
                                    $product = $prodId ? \App\Models\Product::find($prodId) : null;
                                    if ($product && $product->images->isNotEmpty()) {
                                        $firstImage = $product->images->firstWhere('is_thumbnail', true)?->image_path
                                                      ?: $product->images->first()->image_path;
                                        $imageUrl   = cloudinary_url($firstImage);
                                    } else {
                                        $imageUrl = 'https://via.placeholder.com/100?text=NoImg';
                                    }
                                @endphp
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $item['name'] }}"
                                    class="w-24 h-24 object-cover rounded mr-4"
                                >
                                <div>
                                    <a
                                        href="{{ $product ? route('products.show', $product->slug) : '#' }}"
                                        class="text-lg font-medium text-gray-800 hover:text-indigo-600"
                                    >
                                        {{ $item['name'] }}
                                    </a>
                                    @if(count($options) > 1)
                                        <p class="text-sm text-gray-500">
                                            @foreach($options as $key => $value)
                                                @if($key !== 'product_id_original')
                                                    <span>{{ ucfirst($key) }}: {{ $value }}</span>@if(!$loop->last), @endif
                                                @endif
                                            @endforeach
                                        </p>
                                    @endif
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ number_format($item['price'], 0, ',', '.') }} VNĐ
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 sm:space-x-6 w-full sm:w-auto">
                                <div class="flex items-center border border-gray-300 rounded">
                                    <button
                                        wire:click="decrementQuantity('{{ $item['rowId'] }}')"
                                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 focus:outline-none disabled:opacity-50"
                                        @if($item['qty'] <= 1) disabled @endif
                                        wire:loading.attr="disabled"
                                        wire:target="decrementQuantity"
                                    >-</button>

                                    <input
                                        type="number"
                                        value="{{ $item['qty'] }}"
                                        min="1"
                                        class="w-12 text-center border-l border-r border-gray-300 focus:outline-none"
                                        wire:change="updateQuantity('{{ $item['rowId'] }}', $event.target.value)"
                                        wire:loading.attr="disabled"
                                        wire:target="updateQuantity"
                                    >

                                    <button
                                        wire:click="incrementQuantity('{{ $item['rowId'] }}')"
                                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 focus:outline-none"
                                        wire:loading.attr="disabled"
                                        wire:target="incrementQuantity"
                                    >+</button>
                                </div>
                                <p class="font-semibold text-gray-800 w-24 text-right">
                                    {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }} VNĐ
                                </p>
                                <button
                                    wire:click="removeItem('{{ $item['rowId'] }}')"
                                    class="text-gray-500 hover:text-red-600 focus:outline-none"
                                    wire:loading.attr="disabled"
                                    wire:target="removeItem"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 
                                                 111.414 1.414L11.414 10l4.293 4.293a1 1 0 
                                                 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 
                                                 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 
                                                 010-1.414z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-between items-center">
                    <a href="{{ route('products.index') }}"
                       class="text-indigo-600 hover:underline font-medium">
                        ← Tiếp tục mua sắm
                    </a>
                    <button
                        wire:click="clearCart"
                        onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')"
                        class="px-4 py-2 text-sm text-red-600 border border-red-600 rounded hover:bg-red-50 focus:outline-none"
                    >
                        Xóa toàn bộ giỏ hàng
                    </button>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md sticky top-8">
                    <h2 class="text-xl font-semibold mb-4">Tóm Tắt Đơn Hàng</h2>

                    <div class="space-y-2 mb-4 border-b pb-3">
                        <div class="flex justify-between">
                            <span>Tạm tính:</span>
                            <span>{{ number_format($cartSubtotal, 0, ',', '.') }} VNĐ</span>
                        </div>
                        @if($cartTax > 0)
                            <div class="flex justify-between">
                                <span>Thuế:</span>
                                <span>{{ number_format($cartTax, 0, ',', '.') }} VNĐ</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center font-bold text-lg mb-6">
                        <span>Tổng cộng:</span>
                        <span>{{ number_format($cartTotal, 0, ',', '.') }} VNĐ</span>
                    </div>

                    <a href="{{ route('checkout.index') }}"
                       class="block w-full text-center px-6 py-3 bg-indigo-600 text-white font-semibold 
                              rounded-md hover:bg-indigo-700 transition-colors">
                        Tiến Hành Thanh Toán
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-white p-6 rounded-lg shadow-md">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 
                         2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 
                         2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 
                         0 014 0z"/>
            </svg>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">
                Giỏ hàng của bạn đang trống
            </h2>
            <p class="text-gray-500 mb-6">
                Hãy thêm sản phẩm vào giỏ để tiếp tục mua sắm nhé!
            </p>
            <a href="{{ route('products.index') }}"
               class="inline-block px-8 py-3 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700">
                Khám Phá Sản Phẩm
            </a>
        </div>
    @endif
</div>
