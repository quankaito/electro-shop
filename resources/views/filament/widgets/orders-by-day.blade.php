{{-- resources/views/filament/widgets/orders-by-day.blade.php --}}

<x-filament::widget>
    <x-filament::card>
        {{-- Tiêu đề (heading) sẽ được lấy từ getHeading() --}}
        <h2 class="text-xl font-semibold">
            {{ $this->getHeading() }}
        </h2>

        {{-- Phần biểu đồ: Filament sẽ render chart dựa trên getData() --}}
        <div class="mt-4">
            {{ $this->chart }}
        </div>
    </x-filament::card>
</x-filament::widget>
