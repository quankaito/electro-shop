@extends('layouts.app')

@section('title', 'Sản phẩm ' . $category->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full md:w-1/4 lg:w-1/5">
            {{-- Placeholder cho bộ lọc (sẽ được thay thế bằng Livewire component) --}}
            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-lg font-semibold mb-3">Bộ Lọc</h3>
                 {{-- Có thể hiển thị danh mục con của $category hiện tại --}}
                @if($category->children->isNotEmpty())
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700">Danh mục con:</h4>
                    <ul class="mt-1 space-y-1">
                        @foreach($category->children as $child)
                        <li>
                            <a href="{{ route('products.category', $child->slug) }}" class="text-sm text-indigo-600 hover:underline">{{ $child->name }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <p class="text-sm text-gray-500">Bộ lọc sản phẩm sẽ được hiển thị ở đây.</p>
            </div>
        </aside>

        <!-- Product Grid -->
        <main class="w-full md:w-3/4 lg:w-4/5">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-semibold">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="mt-2 text-sm text-gray-600">{{ $category->description }}</p>
                    @endif
                </div>
                <div>
                    {{-- Placeholder cho sắp xếp --}}
                    <select class="border-gray-300 rounded-md shadow-sm">
                        <option>Sắp xếp theo...</option>
                    </select>
                </div>
            </div>

            {{-- Đây là nơi component Livewire ProductList sẽ được nhúng, truyền category slug vào --}}
            {{-- @livewire('frontend.product.product-list', ['categorySlug' => $category->slug]) --}}
            @livewire('frontend.product.product-list', ['categorySlug' => $category->slug])
            <!-- <div class="text-center p-10 border-2 border-dashed border-gray-300 rounded-lg">
                <p class="text-gray-500">Livewire Component "ProductList" (với categorySlug="{{$category->slug}}") sẽ hiển thị tại đây.</p>
            </div> -->
        </main>
    </div>
</div>
@endsection