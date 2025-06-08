{{-- resources/views/frontend/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Trang Chủ')

@section('content')
    @php
        // Lấy cloud name và base URL của Cloudinary
        $cloudName    = config('cloudinary.cloud_name');
        $baseCloudUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/";
    @endphp

    <!-- 1. Hero Section / Banners -->
    @if($homeBanners->isNotEmpty())
        <section class="mb-12">
            <div class="relative" x-data="{ activeSlide: 1, slides: {{ $homeBanners->count() }} }">
                @foreach($homeBanners as $index => $banner)
                    @php
                        // Build URL cho banner
                        $transformBanner = 'c_fill,w_1200,h_500,f_auto,q_auto';
                        $publicIdBanner  = $banner->image_url_desktop;
                        $urlBanner       = $baseCloudUrl . $transformBanner . '/' . $publicIdBanner;
                    @endphp

                    <div x-show="activeSlide === {{ $index + 1 }}">
                        <a href="{{ $banner->link_url ?? '#' }}">
                            <img
                                src="{{ $urlBanner }}"
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

    <!-- 2. Khuyến mãi nổi bật -->
    @if($featuredPromotion)
        <section class="bg-gray-800 text-white py-12 mb-12">
            <div class="container mx-auto px-4 text-center"
                x-data="countdown('{{ $featuredPromotion->end_date->toIso8601String() }}')"
                x-init="init()">
                
                <h2 class="text-3xl md:text-4xl font-bold uppercase tracking-wider mb-2">
                    {{ $featuredPromotion->name }}
                </h2>
                <p class="text-lg text-yellow-400 mb-6">
                    Ưu đãi sẽ kết thúc trong...
                </p>

                {{-- Đồng hồ đếm ngược --}}
                <div class="flex items-center justify-center space-x-4 md:space-x-8 text-center mb-8">
                    <div>
                        <div class="text-4xl md:text-6xl font-bold" x-text="days">00</div>
                        <div class="text-sm uppercase text-gray-400">Ngày</div>
                    </div>
                    <div class="text-4xl md:text-6xl font-bold pb-6">:</div>
                    <div>
                        <div class="text-4xl md:text-6xl font-bold" x-text="hours">00</div>
                        <div class="text-sm uppercase text-gray-400">Giờ</div>
                    </div>
                    <div class="text-4xl md:text-6xl font-bold pb-6">:</div>
                    <div>
                        <div class="text-4xl md:text-6xl font-bold" x-text="minutes">00</div>
                        <div class="text-sm uppercase text-gray-400">Phút</div>
                    </div>
                    <div class="text-4xl md:text-6xl font-bold pb-6">:</div>
                    <div>
                        <div class="text-4xl md:text-6xl font-bold" x-text="seconds">00</div>
                        <div class="text-sm uppercase text-gray-400">Giây</div>
                    </div>
                </div>

                {{-- Mã giảm giá và nút CTA --}}
                <p class="mb-4">Sử dụng mã dưới đây khi thanh toán:</p>
                <div x-data="{ copied: false }" class="inline-flex items-center bg-gray-700 border border-dashed border-gray-500 rounded-lg p-2 mb-8">
                    <span class="font-mono text-xl text-yellow-300 mr-4">{{ $featuredPromotion->code }}</span>
                    <button @click="navigator.clipboard.writeText('{{ $featuredPromotion->code }}'); copied = true; setTimeout(() => copied = false, 2000)" class="text-sm bg-gray-600 hover:bg-gray-500 rounded px-3 py-1 transition">
                        <span x-show="!copied">Sao chép</span>
                        <span x-show="copied" class="text-green-400">Đã chép!</span>
                    </button>
                </div>
                
                <div>
                    <a href="{{ route('products.index') }}" 
                       class="bg-yellow-500 text-gray-900 font-bold uppercase py-3 px-8 rounded-lg hover:bg-yellow-400 transition-colors duration-300">
                        Mua sắm ngay
                    </a>
                </div>
            </div>
        </section>

        @push('scripts')
        <script>
            function countdown(endDate) {
                return {
                    endDate: new Date(endDate),
                    days: '00', hours: '00', minutes: '00', seconds: '00', interval: null,
                    init() {
                        this.updateTime();
                        this.interval = setInterval(() => { this.updateTime(); }, 1000);
                    },
                    updateTime() {
                        const diff = this.endDate.getTime() - new Date().getTime();
                        if (diff <= 0) {
                            clearInterval(this.interval);
                            this.days = this.hours = this.minutes = this.seconds = '00';
                            return;
                        }
                        this.days    = this.pad(Math.floor(diff / (1000 * 60 * 60 * 24)));
                        this.hours   = this.pad(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
                        this.minutes = this.pad(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)));
                        this.seconds = this.pad(Math.floor((diff % (1000 * 60)) / 1000));
                    },
                    pad: num => String(num).padStart(2, '0')
                }
            }
        </script>
        @endpush
    @endif

    <div class="container mx-auto px-4">
        <!-- 3. Khám Phá Danh Mục -->
        @if(isset($categoriesForHome) && $categoriesForHome->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-3xl font-semibold mb-6 text-center">Khám Phá Danh Mục</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-6">
                    @foreach($categoriesForHome as $category)
                        @php
                            // Build URL cho ảnh category
                            $transformCat = 'c_fill,w_400,h_160,f_auto,q_auto';
                            $pubCat       = $category->image_path;
                            $urlCat       = $baseCloudUrl . $transformCat . '/' . $pubCat;
                        @endphp
                        <a href="{{ route('products.category', $category->slug) }}"
                           class="group block bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                            <div class="w-full h-40 bg-gray-100 overflow-hidden">
                                @if($category->image_path)
                                    <img src="{{ $urlCat }}"
                                         alt="{{ $category->name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-gray-400">Chưa có ảnh</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 text-center">
                                <h3 class="text-lg font-medium text-gray-800 group-hover:text-indigo-600 transition-colors">
                                    {{ $category->name }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 4. Thương Hiệu -->
        @if(isset($brandsForHome) && $brandsForHome->isNotEmpty())
            <section class="mb-12">
                <h2 class="text-3xl font-semibold mb-6 text-center">Thương Hiệu Nổi Bật</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-6">
                    @foreach($brandsForHome as $brand)
                        @php
                            // Build URL cho logo brand
                            $transformBrand = 'c_fill,h_80,f_auto,q_auto';
                            $pubBrand       = $brand->logo;
                            $urlBrand       = $baseCloudUrl . $transformBrand . '/' . $pubBrand;
                        @endphp
                        <a href="{{ route('products.index', ['brand' => $brand->slug]) }}"
                           class="group block bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                            <div class="w-full h-40 bg-gray-50 flex items-center justify-center p-4">
                                @if($brand->logo)
                                    <img src="{{ $urlBrand }}"
                                         alt="{{ $brand->name }}"
                                         class="max-h-20 object-contain transition-transform duration-300 group-hover:scale-110">
                                @else
                                    <span class="text-gray-400">Chưa có logo</span>
                                @endif
                            </div>
                            <div class="p-4 text-center">
                                <h3 class="text-lg font-medium text-gray-800 group-hover:text-indigo-600 transition-colors">
                                    {{ $brand->name }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
        
        <!-- 5. Sản phẩm nổi bật -->
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

        <!-- 6. Sản phẩm mới -->
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
    </div>
@endsection
