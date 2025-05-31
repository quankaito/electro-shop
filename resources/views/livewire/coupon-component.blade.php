<div class="border-t pt-4">
    <label class="block text-sm font-medium text-gray-700">Mã giảm giá</label>
    <div class="mt-1 flex rounded-md shadow-sm">
        <input
            type="text"
            wire:model.defer="couponCode"
            placeholder="Nhập mã"
            class="flex-1 border border-gray-300 rounded-l-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <button
            type="button"
            wire:click="applyCoupon"
            wire:loading.attr="disabled"
            class="px-4 bg-indigo-600 text-white border border-l-0 rounded-r-md hover:bg-indigo-700 focus:outline-none"
        >
            Áp dụng
        </button>
    </div>

    @error('couponCode')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror

    @if (session()->has('success'))
        <div class="mt-2 text-green-600 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($appliedPromotion)
        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded flex justify-between items-center">
            <div class="text-sm text-green-800">
                ✨ Áp dụng mã
                <span class="font-semibold">{{ $appliedPromotion->code }}</span>:
                −{{ number_format($discountAmount, 0, ',', '.') }} VNĐ
            </div>
            <button
                type="button"
                wire:click="removeCoupon"
                wire:loading.attr="disabled"
                class="text-red-500 text-sm hover:underline focus:outline-none"
            >× Xóa</button>
        </div>
    @endif
</div>
