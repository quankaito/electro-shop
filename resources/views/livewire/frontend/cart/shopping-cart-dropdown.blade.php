{{-- resources/views/livewire/frontend/cart/shopping-cart-dropdown.blade.php --}}

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative text-gray-600 hover:text-indigo-600 focus:outline-none" title="Giỏ hàng">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 
                     2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 
                     2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 
                     0 014 0z"></path>
        </svg>
        @if($cartCount > 0)
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-semibold rounded-full px-1.5 py-0.5 leading-tight">
                {{ $cartCount }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-xl z-50 overflow-hidden"
         style="display: none;">
        <div class="p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Giỏ Hàng Của Bạn</h3>
            
            @if($cartItems && count($cartItems) > 0)
                {{-- Danh sách item --}}
                <div class="max-h-60 overflow-y-auto space-y-3 mb-4 pr-2 -mr-2 custom-scrollbar">
                    @foreach($cartItems as $item)
                        <div class="flex items-start space-x-3" wire:key="cart-dropdown-{{ $item['rowId'] }}">
                            @php
                                $product = isset($item['options']['product_id_original']) 
                                    ? \App\Models\Product::find($item['options']['product_id_original']) 
                                    : null;

                                if ($product && $product->images->isNotEmpty()) {
                                    // Lấy ảnh thumbnail (nếu có) hoặc ảnh đầu tiên
                                    $imgPath = $product->images->firstWhere('is_thumbnail', true)?->image_path
                                                ?: $product->images->first()->image_path;

                                    // Lấy URL từ Cloudinary
                                    $imageUrl = cloudinary_url($imgPath);
                                } else {
                                    $imageUrl = 'https://via.placeholder.com/64?text=NoImg';
                                }
                            @endphp

                            <img src="{{ $imageUrl }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded">
                            <div class="flex-grow">
                                <a href="{{ $product ? route('products.show', $product->slug) : '#' }}"
                                   class="text-sm font-medium text-gray-800 hover:text-indigo-600 leading-tight block">
                                    {{ $item['name'] }}
                                </a>
                                @if(count($item['options']) > 1)
                                    <p class="text-xs text-gray-500">
                                        @foreach($item['options'] as $key => $value)
                                            @if($key !== 'product_id_original')
                                                <span>{{ ucfirst($key) }}: {{ $value }}</span>@if(!$loop->last), @endif
                                            @endif
                                        @endforeach
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500">{{ $item['qty'] }} x {{ number_format($item['price'], 0, ',', '.') }} VNĐ</p>
                            </div>
                            <button wire:click="removeItem('{{ $item['rowId'] }}')" 
                                    wire:loading.attr="disabled" 
                                    wire:target="removeItem('{{ $item['rowId'] }}')"
                                    class="text-gray-400 hover:text-red-500 focus:outline-none">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 
                                             1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 
                                             0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 
                                             0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 
                                             0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Tóm tắt đơn hàng (subtotal + tax + buttons) --}}
                <div class="border-t pt-3">
                    <div class="flex justify-between items-center text-sm font-medium text-gray-800">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($cartSubtotal, 0, ',', '.') }} VNĐ</span>
                    </div>

                    {{-- Dòng thuế --}}
                    @if($cartTax > 0)
                        <div class="flex justify-between items-center text-sm font-medium text-gray-800 mt-1">
                            <span>Thuế:</span>
                            <span>{{ number_format($cartTax, 0, ',', '.') }} VNĐ</span>
                        </div>
                    @endif

                    <a href="{{ route('cart.index') }}"
                       class="block w-full mt-4 px-4 py-2 bg-gray-200 text-gray-800 text-center rounded-md hover:bg-gray-300 font-medium">
                        Xem Giỏ Hàng
                    </a>
                    <a href="{{ route('checkout.index') }}"
                       class="block w-full mt-2 px-4 py-2 bg-indigo-600 text-white text-center rounded-md hover:bg-indigo-700 font-medium">
                        Thanh Toán
                    </a>
                </div>
            @else
                <p class="text-center text-gray-500 py-8">Giỏ hàng của bạn đang trống.</p>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c5c5c5;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush
