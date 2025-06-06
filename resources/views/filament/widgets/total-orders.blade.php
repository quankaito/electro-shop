{{-- Đường dẫn: resources/views/filament/widgets/total-orders.blade.php --}}
<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Tổng số đơn hàng</h2>
                {{-- Biến $count do getData() cung cấp --}}
                <p class="text-3xl mt-2">{{ $count }}</p>
            </div>
            <div>
                <x-heroicon-o-shopping-cart class="h-12 w-12 text-gray-400" />
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
