{{-- resources/views/frontend/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Trang Chủ')

@section('content')
    <!-- Hero Section / Banners -->
    @if($homeBanners->isNotEmpty())
        <section class="mb-12">
            <div class="relative" x-data="{ activeSlide: 1, slides: {{ $homeBanners->count() }} }">
                @foreach($homeBanners as $index => $banner)
                    <div x-show="activeSlide === {{ $index + 1 }}">
                        <a href="{{ $banner->link_url ?? '#' }}">
                            <img
                                src="{{ Storage::disk('cloudinary')->url($banner->image_url_desktop) }}"
                                alt="{{ $banner->title }}"
                                class="w-full h-auto object-cover"
                                style="max-height: 500px;"
                            >
                        </a>
                    </div>
                @endforeach

                @if($homeBanners->count() > 1)
                    <div class="absolute inset-0 flex items-center justify-between p-4">
                        <button
                            @click="activeSlide = activeSlide === 1 ? slides : activeSlide - 1"
                            class="bg-black bg-opacity-50 text-white p-2 rounded-full"
                        >‹</button>
                        <button
                            @click="activeSlide = activeSlide === slides ? 1 : activeSlide + 1"
                            class="bg-black bg-opacity-50 text-white p-2 rounded-full"
                        >›</button>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <div class="container mx-auto px-4">
        <!-- Sản phẩm nổi bật -->
        @if($featuredProducts->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-3xl font-semibold mb-6 text-center">Sản Phẩm Nổi Bật</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($featuredProducts as $product)
                        @include('frontend.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Sản phẩm mới -->
        @if($newProducts->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-3xl font-semibold mb-6 text-center">Hàng Mới Về</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($newProducts as $product)
                        @include('frontend.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Khám Phá Danh Mục -->
        @if(isset($categoriesForHome) && $categoriesForHome->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-3xl font-semibold mb-6 text-center">Khám Phá Danh Mục</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($categoriesForHome as $category)
                        <a href="{{ route('products.category', $category->slug) }}"
                           class="group block bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                            {{-- Nếu Category có trường image_path, hiển thị --}}
                            @if(isset($category->image_path) && $category->image_path)
                                <div class="w-full h-40 bg-gray-100 overflow-hidden">
                                    <img
                                        src="{{ Storage::disk('cloudinary')->url($category->image_path) }}"
                                        alt="{{ $category->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                    >
                                </div>
                            @else
                                <div class="w-full h-40 bg-gray-100 flex items-center justify-center">
                                    <span class="text-gray-400">Chưa có ảnh</span>
                                </div>
                            @endif

                            <div class="p-4 text-center">
                                <h3
                                    class="text-lg font-medium text-gray-800 group-hover:text-indigo-600 transition-colors"
                                >
                                    {{ $category->name }}
                                </h3>
                                {{-- Nếu muốn hiển thị số lượng sản phẩm, có thể dùng $category->products_count --}}
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Thương Hiệu (Brands) -->
        @if(isset($brandsForHome) && $brandsForHome->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-3xl font-semibold mb-6 text-center">Thương Hiệu</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 items-center">
                    @foreach($brandsForHome as $brand)
                        <a href="{{ route('products.index', ['brand' => $brand->slug]) }}"
                           class="flex items-center justify-center bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow duration-300">
                            @if($brand->logo)
                                <img
                                    src="{{ Storage::disk('cloudinary')->url($brand->logo) }}"
                                    alt="{{ $brand->name }}"
                                    class="max-h-12 object-contain"
                                >
                            @else
                                <span class="text-gray-500">{{ $brand->name }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Các section khác nếu cần -->
    </div>
@endsection
