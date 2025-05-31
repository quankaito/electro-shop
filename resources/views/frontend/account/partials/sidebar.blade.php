<aside class="w-full md:w-1/4">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Tài Khoản Của Tôi</h3>
        <nav class="space-y-2">
            <a href="{{ route('account.dashboard') }}"
               class="block px-4 py-2 rounded-md text-sm font-medium
                      {{ request()->routeIs('account.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Bảng Điều Khiển
            </a>
            <a href="{{ route('account.profile') }}"
               class="block px-4 py-2 rounded-md text-sm font-medium
                      {{ request()->routeIs('account.profile') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Thông Tin Cá Nhân
            </a>
            <a href="{{ route('account.orders') }}"
               class="block px-4 py-2 rounded-md text-sm font-medium
                      {{ request()->routeIs('account.orders') || request()->routeIs('account.orders.detail') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Lịch Sử Đơn Hàng
            </a>
            <a href="{{ route('account.addresses') }}"
               class="block px-4 py-2 rounded-md text-sm font-medium
                      {{ request()->routeIs('account.addresses') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Sổ Địa Chỉ
            </a>
            <a href="{{ route('account.wishlist') }}"
                class="block px-4 py-2 rounded-md text-sm font-medium
                    {{ request()->routeIs('account.wishlist') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Sản Phẩm Yêu Thích
            </a>
            <a href="{{ route('account.password.change') }}"
               class="block px-4 py-2 rounded-md text-sm font-medium
                      {{ request()->routeIs('account.password.change') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Đổi Mật Khẩu
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    Đăng Xuất
                </button>
            </form>
        </nav>
    </div>
</aside>