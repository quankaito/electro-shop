<div class="flex items-center space-x-3">
    @if(!$variantId) {{-- Chỉ hiển thị input số lượng nếu không phải là variant (variant sẽ có nút riêng hoặc logic khác) --}}
    <div class="flex items-center border border-gray-300 rounded">
        <button wire:click="$set('quantity', {{ max(1, $quantity - 1) }})"
                class="px-3 py-2 text-gray-600 hover:bg-gray-100 focus:outline-none"
                @if($quantity <= 1) disabled @endif>
            -
        </button>
        <input type="number" wire:model.lazy="quantity"
               class="w-12 text-center border-l border-r border-gray-300 focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
               min="1">
        <button wire:click="$set('quantity', {{ $quantity + 1 }})"
                class="px-3 py-2 text-gray-600 hover:bg-gray-100 focus:outline-none">
            +
        </button>
    </div>
    @endif

    <button wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart"
            class="flex-grow px-6 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors disabled:opacity-50">
        <span wire:loading.remove wire:target="addToCart">
            <svg class="inline-block w-5 h-5 mr-1 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Thêm vào giỏ
        </span>
        <span wire:loading wire:target="addToCart">
            Đang thêm...
        </span>
    </button>
</div>