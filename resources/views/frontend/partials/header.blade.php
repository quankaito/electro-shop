@php
    // Nếu cần, bạn có thể lấy tất cả các categories trong view (không khuyến khích nếu performance quan trọng,
    // tốt hơn nên bind qua View Composer hoặc controller). Ví dụ:
    use App\Models\Category;
    $allCategories = Category::whereNull('parent_id')
                             ->where('is_active', true)
                             ->with('children')
                             ->orderBy('name')
                             ->get();
@endphp

<header class="bg-white shadow-md sticky top-0 z-40">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-4">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">
                    Electro<span class="text-gray-800">Shop</span>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="hidden md:flex flex-grow max-w-xl mx-4">
                <form action="{{ route('products.index') }}" method="GET" class="w-full flex">
                    <input
                        type="text"
                        name="q"
                        placeholder="Tìm kiếm sản phẩm..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        value="{{ request('q') }}"
                    >
                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Header Icons & Auth Links -->
            <div class="flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600">Đăng Nhập</a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Đăng Ký</a>
                @else
                    <!-- Wishlist -->
                    <a href="{{ route('account.wishlist') }}" class="relative text-gray-600 hover:text-indigo-600" title="Danh sách yêu thích">
                        @livewire('frontend.wishlist-count')
                    </a>

                    <!-- Cart -->
                    <div class="relative">
                        @livewire('frontend.cart.shopping-cart-dropdown')
                    </div>

                    <!-- User Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 focus:outline-none">
                            @if(Auth::user()->avatar)
                                <img
                                    src="{{ cloudinary_url(Auth::user()->avatar) }}"
                                    alt="{{ Auth::user()->name }}"
                                    class="w-8 h-8 rounded-full mr-2 object-cover"
                                >
                            @else
                                <span
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-300 text-gray-700 mr-2">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            @endif
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="ml-1 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0
                                         01-1.414 0l-4-4a1 1 0 010-1.414z"
                                      clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <a href="{{ route('account.dashboard') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tài Khoản</a>
                            <a href="{{ route('account.orders') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Đơn Hàng</a>
                            <a href="{{ route('dashboard') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('filament.admin.pages.dashboard') }}" target="_blank"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Trang Quản Trị</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Đăng Xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>

        <!-- Main Navigation -->
        <nav class="bg-gray-50 border-t border-b border-gray-200">
            <div class="container mx-auto px-4">
                <ul class="flex items-center justify-center space-x-6 h-12 text-sm font-medium">
                    <li>
                        <a href="{{ route('home') }}"
                           class="text-gray-700 hover:text-indigo-600 {{ request()->routeIs('home') ? 'text-indigo-600 font-semibold' : '' }}">
                            Trang Chủ
                        </a>
                    </li>

                    <!-- Dropdown “Sản Phẩm” -->
                    <li x-data="{ open: false }" class="relative">
                        <button
                            @mouseenter="open = true"
                            @mouseleave="open = false"
                            class="flex items-center text-gray-700 hover:text-indigo-600 focus:outline-none"
                        >
                            <span class="{{ request()->routeIs('products.index') || request()->routeIs('products.category') ? 'text-indigo-600 font-semibold' : '' }}">
                                Sản Phẩm
                            </span>
                            <svg class="ml-1 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div 
                            x-show="open" 
                            @mouseenter="open = true" 
                            @mouseleave="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute left-0 mt-2 w-56 bg-white rounded-md shadow-lg overflow-hidden z-50"
                            style="display: none;"
                        >
                            <ul class="divide-y divide-gray-200">
                                {{-- Thêm mục “Tất Cả Sản Phẩm” --}}
                                <li>
                                    <a href="{{ route('products.index') }}"
                                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100 font-semibold">
                                        Tất Cả Sản Phẩm
                                    </a>
                                </li>

                                @foreach($allCategories as $cat)
                                    <li>
                                        <a href="{{ route('products.category', $cat->slug) }}"
                                           class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                            {{ $cat->name }}
                                        </a>
                                        @if($cat->children->isNotEmpty())
                                            <ul class="pl-4 bg-gray-50">
                                                @foreach($cat->children as $child)
                                                    <li>
                                                        <a href="{{ route('products.category', $child->slug) }}"
                                                           class="block px-4 py-2 text-gray-600 hover:bg-gray-100">
                                                            {{ $child->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>

                    {{-- Bạn có thể thêm các menu khác ở đây --}}
                    <li>
                        <a href="{{ route('blog.index') }}"
                           class="text-gray-700 hover:text-indigo-600 {{ request()->routeIs('blog.index') || request()->routeIs('blog.show') ? 'text-indigo-600 font-semibold' : '' }}">
                            Tin Tức
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                           class="text-gray-700 hover:text-indigo-600 {{ request()->routeIs('contact') ? 'text-indigo-600 font-semibold' : '' }}">
                            Liên Hệ
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq') }}"
                           class="text-gray-700 hover:text-indigo-600 {{ request()->routeIs('faq') ? 'text-indigo-600 font-semibold' : '' }}">
                            FAQ
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Mobile Search Bar (hiển thị trên mobile, ẩn trên md trở lên) -->
        <div class="md:hidden p-4 bg-gray-50 border-b">
            <form action="{{ route('products.index') }}" method="GET" class="w-full flex">
                <input type="text"
                       name="q"
                       placeholder="Tìm kiếm sản phẩm..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       value="{{ request('q') }}"
                >
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>
