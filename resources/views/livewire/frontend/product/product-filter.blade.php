<div class="bg-white p-4 rounded shadow space-y-6">
    <h3 class="text-lg font-semibold mb-3">Bộ Lọc</h3>

    {{-- 1. Danh mục con --}}
    @if($subcategoryList->isNotEmpty())
        <div>
            <h4 class="font-medium text-gray-700 mb-2">Danh mục con</h4>
            <ul class="space-y-1 max-h-40 overflow-y-auto">
                @foreach($subcategoryList as $sub)
                    <li class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="selectedSubcategories"
                            value="{{ $sub->id }}"
                            id="subcat-{{ $sub->id }}"
                            class="mr-2 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                        >
                        <label for="subcat-{{ $sub->id }}" class="text-sm text-gray-700">{{ $sub->name }}</label>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 2. Thương hiệu --}}
    @if($brandList->isNotEmpty())
        <div>
            <h4 class="font-medium text-gray-700 mb-2">Thương hiệu</h4>
            <ul class="space-y-1 max-h-32 overflow-y-auto">
                @foreach($brandList as $brand)
                    <li class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="selectedBrands"
                            value="{{ $brand->id }}"
                            id="brand-{{ $brand->id }}"
                            class="mr-2 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                        >
                        <label for="brand-{{ $brand->id }}" class="text-sm text-gray-700">{{ $brand->name }}</label>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 3. Khoảng giá --}}
    <div>
        <h4 class="font-medium text-gray-700 mb-2">Giá (₫)</h4>
        <div class="flex items-center space-x-2">
            <input
                type="number"
                wire:model="minPrice"
                min="{{ $priceRange['min'] }}"
                max="{{ $priceRange['max'] }}"
                class="w-1/2 border-gray-300 rounded-md px-2 py-1"
                placeholder="Thấp nhất"
            >
            <span class="text-gray-500">–</span>
            <input
                type="number"
                wire:model="maxPrice"
                min="{{ $priceRange['min'] }}"
                max="{{ $priceRange['max'] }}"
                class="w-1/2 border-gray-300 rounded-md px-2 py-1"
                placeholder="Cao nhất"
            >
        </div>
        <p class="mt-1 text-xs text-gray-500">Khoảng: {{ number_format($priceRange['min']) }}₫ – {{ number_format($priceRange['max']) }}₫</p>
    </div>

    {{-- 4. Nút Áp dụng và Đặt lại --}}
    <div class="flex space-x-2">
        <button
            wire:click.prevent="applyFilters"
            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 rounded"
        >
            Áp dụng
        </button>
        <button
            wire:click.prevent="resetFilters"
            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium py-2 rounded"
        >
            Đặt lại
        </button>
    </div>
</div>
