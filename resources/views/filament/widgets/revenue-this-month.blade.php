{{-- resources/views/filament/widgets/revenue-this-month.blade.php --}}

<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Doanh thu tháng này</h2>
                {{-- Hiển thị biến $revenue --}}
                <p class="text-3xl mt-2">{{ $revenue }}</p>
            </div>
            <div>
                <x-heroicon-o-cash class="h-12 w-12 text-gray-400" />
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
