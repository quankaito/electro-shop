{{-- resources/views/livewire/frontend/product/product-list.blade.php --}}
<div class="flex flex-col md:flex-row gap-8">
    {{-- Sidebar Filters --}}
    <aside class="w-full md:w-1/4 lg:w-1/5">
        <div class="bg-white p-4 rounded shadow space-y-6">
            <h3 class="text-lg font-semibold mb-3">Bộ Lọc</h3>

            {{-- 1. Search --}}
            <div>
                <label for="searchQuery" class="text-sm font-medium text-gray-700">Tìm kiếm:</label>
                <input
                    type="text"
                    id="searchQuery"
                    wire:model.debounce.500ms="searchQuery"
                    placeholder="Từ khóa..."
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>

            {{-- 2. Category Con (nếu có) --}}
            @if($categorySlug)
                @php
                    $currentCategory = \App\Models\Category::where('slug', $categorySlug)
                                                           ->with('children')
                                                           ->first();
                @endphp

                @if($currentCategory && $currentCategory->children->isNotEmpty())
                    <div>
                        <p class="font-medium text-gray-700 mb-2">Danh mục con:</p>
                        <ul class="space-y-1 max-h-40 overflow-y-auto">
                            @foreach($currentCategory->children as $sub)
                                <li class="flex items-center">
                                    <input
                                        type="checkbox"
                                        wire:model="categoryIds"
                                        value="{{ $sub->id }}"
                                        id="subcat-{{ $sub->id }}"
                                        class="mr-2 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                    >
                                    <label for="subcat-{{ $sub->id }}" class="text-sm text-gray-700">
                                        {{ $sub->name }}
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif

            {{-- 3. Thương hiệu --}}
            @if($brandsForFilter->isNotEmpty())
                <div>
                    <p class="font-medium text-gray-700 mb-2">Thương hiệu:</p>
                    <ul class="space-y-1 max-h-32 overflow-y-auto">
                        @foreach($brandsForFilter as $brand)
                            <li class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model="brandSlugs"
                                    value="{{ $brand->slug }}"
                                    id="brand-{{ $brand->slug }}"
                                    class="mr-2 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                >
                                <label for="brand-{{ $brand->slug }}" class="text-sm text-gray-700">
                                    {{ $brand->name }}
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 4. Khoảng giá --}}
            <div>
                <p class="font-medium text-gray-700 mb-2">Giá (₫):</p>
                <div class="flex items-center space-x-2">
                    <input
                        type="number"
                        wire:model.defer="priceRange.min"
                        min="{{ $priceRangeAll['min'] }}"
                        max="{{ $priceRangeAll['max'] }}"
                        class="w-1/2 border-gray-300 rounded-md px-2 py-1"
                        placeholder="{{ number_format($priceRangeAll['min']) }}"
                    >
                    <span class="text-gray-500">–</span>
                    <input
                        type="number"
                        wire:model.defer="priceRange.max"
                        min="{{ $priceRangeAll['min'] }}"
                        max="{{ $priceRangeAll['max'] }}"
                        class="w-1/2 border-gray-300 rounded-md px-2 py-1"
                        placeholder="{{ number_format($priceRangeAll['max']) }}"
                    >
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Khoảng: {{ number_format($priceRangeAll['min']) }}₫ – {{ number_format($priceRangeAll['max']) }}₫
                </p>
            </div>

            {{-- 5. Attributes --}}
            @if($attributesForFilter->isNotEmpty())
                @foreach($attributesForFilter as $attribute)
                    @if($attribute->values->isNotEmpty())
                        <div>
                            <p class="font-medium text-gray-700 mb-2">{{ $attribute->name }}:</p>
                            <ul class="space-y-1 max-h-32 overflow-y-auto">
                                @foreach($attribute->values as $val)
                                    <li class="flex items-center">
                                        <input
                                            type="checkbox"
                                            wire:model="selectedAttributes.{{ $attribute->slug }}"
                                            value="{{ $val->slug }}"
                                            id="attr-{{ $attribute->slug }}-{{ $val->slug }}"
                                            class="mr-2 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                        >
                                        <label for="attr-{{ $attribute->slug }}-{{ $val->slug }}" class="text-sm text-gray-700">
                                            {{ $val->name }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
            @endif

            {{-- 6. Nút Áp dụng & Đặt lại --}}
            <div class="flex space-x-2 pt-4">
                <button
                    wire:click.prevent="applyFilter"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 rounded"
                >
                    Áp dụng
                </button>
                <button
                    wire:click.prevent="$reset(['brandSlugs','searchQuery','priceRange','selectedAttributes','categoryIds','sortBy'])"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium py-2 rounded"
                >
                    Đặt lại
                </button>
            </div>
        </div>
    </aside>

    {{-- Product Grid & Sort --}}
    <main class="w-full md:w-3/4 lg:w-4/5">
        {{-- Thanh tiêu đề + Sort --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0">
            <div>
                @if($categorySlug)
                    @php
                        $cat = \App\Models\Category::where('slug', $categorySlug)->first();
                    @endphp
                    <h1 class="text-3xl font-semibold">{{ $cat->name }}</h1>
                @else
                    <h1 class="text-3xl font-semibold">Tất Cả Sản Phẩm</h1>
                @endif
            </div>

            {{-- Sort Dropdown --}}
            <div class="flex items-center space-x-2">
                <label for="sortBy" class="text-sm font-medium text-gray-700">Sắp xếp:</label>
                <select
                    id="sortBy"
                    wire:model="sortBy"
                    class="border-gray-300 rounded-md shadow-sm text-sm"
                >
                    <option value="created_at">Mới nhất</option>
                    <option value="regular_price">Giá tăng dần</option>
                    <option value="-regular_price">Giá giảm dần</option>
                </select>
            </div>
        </div>

        {{-- Grid sản phẩm --}}
        @if($products->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    @include('frontend.partials.product-card', ['product' => $product])
                @endforeach
            </div>

            {{-- Phân trang --}}
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Không tìm thấy sản phẩm nào.</p>
            </div>
        @endif
    </main>
</div>
