@extends('layouts.app')

@section('title', 'Bảng Điều Khiển Tài Khoản')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        @include('frontend.account.partials.sidebar') {{-- Sidebar cho các trang tài khoản --}}

        <main class="w-full md:w-3/4">
            <h1 class="text-2xl font-semibold mb-6">Chào mừng trở lại, {{ $user->name }}!</h1>
            <p class="mb-6 text-gray-700">Từ bảng điều khiển tài khoản, bạn có thể xem các đơn hàng gần đây, quản lý địa chỉ giao hàng và thanh toán, cũng như chỉnh sửa thông tin cá nhân và mật khẩu.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold mb-3">Đơn Hàng Gần Đây</h2>
                    {{-- Hiển thị một vài đơn hàng gần nhất --}}
                    @if($user->orders()->count() > 0)
                        <ul>
                        @foreach($user->orders()->latest()->take(3)->get() as $order)
                            <li class="border-b py-2 last:border-b-0">
                                <a href="{{ route('account.orders.detail', $order) }}" class="text-indigo-600 hover:underline">Đơn hàng #{{ $order->order_number }}</a> - {{ $order->created_at->format('d/m/Y') }} - <span class="capitalize">{{ $order->status }}</span>
                            </li>
                        @endforeach
                        </ul>
                        <a href="{{ route('account.orders') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Xem tất cả đơn hàng →</a>
                    @else
                        <p class="text-gray-600">Bạn chưa có đơn hàng nào.</p>
                    @endif
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold mb-3">Thông Tin Tài Khoản</h2>
                    <p class="text-gray-700"><strong>Tên:</strong> {{ $user->name }}</p>
                    <p class="text-gray-700"><strong>Email:</strong> {{ $user->email }}</p>
                    <p class="text-gray-700"><strong>Số điện thoại:</strong> {{ $user->phone_number ?: 'Chưa cập nhật' }}</p>
                    <a href="{{ route('account.profile') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Chỉnh sửa thông tin →</a>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection