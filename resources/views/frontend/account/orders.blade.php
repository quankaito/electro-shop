@extends('layouts.app')

@section('title', 'Lịch Sử Đơn Hàng')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        @include('frontend.account.partials.sidebar')

        <main class="w-full md:w-3/4">
            <h1 class="text-2xl font-semibold mb-6">Lịch Sử Đơn Hàng</h1>

            @if($orders->isNotEmpty())
                <div class="bg-white shadow overflow-x-auto rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã ĐH</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày Đặt</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng Tiền</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng Thái</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Chi tiết</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($order->status == 'delivered' || $order->status == 'completed') bg-green-100 text-green-800
                                        @elseif($order->status == 'pending' || $order->status == 'processing' || $order->status == 'confirmed' || $order->status == 'payment_pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('account.orders.detail', $order) }}" class="text-indigo-600 hover:text-indigo-900">Xem Chi Tiết</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <p class="text-gray-600">Bạn chưa có đơn hàng nào.</p>
                    <a href="{{ route('products.index') }}" class="mt-4 inline-block text-indigo-600 hover:underline">Bắt đầu mua sắm ngay!</a>
                </div>
            @endif
        </main>
    </div>
</div>
@endsection