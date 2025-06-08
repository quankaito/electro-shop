{{-- resources/views/frontend/account/order-detail.blade.php --}}
@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        @include('frontend.account.partials.sidebar')

        <main class="w-full md:w-3/4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Chi Tiết Đơn Hàng #{{ $order->order_number }}</h1>
                <a href="{{ route('account.orders') }}" class="text-sm text-indigo-600 hover:underline">← Quay lại danh sách đơn hàng</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Thông Tin Đơn Hàng</h3>
                        <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Trạng thái:</strong> <span class="font-medium capitalize">{{ str_replace('_', ' ', $order->status) }}</span></p>
                        <p><strong>Phương thức thanh toán:</strong> {{ $order->paymentMethod?->name ?: 'N/A' }}</p>
                        <p><strong>Phương thức vận chuyển:</strong> {{ $order->shippingMethod?->name ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Địa Chỉ Giao Hàng</h3>
                        @if($order->shippingAddress)
                            <p>{{ $order->shippingAddress->full_name }}</p>
                            <p>{{ $order->shippingAddress->phone_number }}</p>
                            <p>{{ $order->shippingAddress->address_line1 }}</p>
                            @if($order->shippingAddress->address_line2)
                                <p>{{ $order->shippingAddress->address_line2 }}</p>
                            @endif
                            <p>
                                {{ $order->shippingAddress->ward?->name }},
                                {{ $order->shippingAddress->district?->name }},
                                {{ $order->shippingAddress->province?->name }}
                            </p>
                        @else
                            <p>Không có thông tin địa chỉ.</p>
                        @endif
                    </div>
                </div>

                <h3 class="text-lg font-semibold mb-3">Sản Phẩm Trong Đơn Hàng</h3>
                <div class="space-y-4 mb-6">
                    @foreach($order->items as $item)
                        <div class="flex items-center border-b pb-4 last:border-b-0 last:pb-0">
                            @if($item->product && $item->product->images->isNotEmpty())
                                @php
                                    // Lấy ảnh đầu tiên làm thumbnail
                                    $firstImage = $item->product->images->first();
                                @endphp
                                <img
                                    src="{{ cloudinary_url($firstImage->image_path) }}"
                                    alt="{{ $item->product_name }}"
                                    class="w-20 h-20 object-cover rounded mr-4"
                                >
                            @else
                                <img
                                    src="https://via.placeholder.com/80x80?text=No+Image"
                                    alt="{{ $item->product_name }}"
                                    class="w-20 h-20 object-cover rounded mr-4"
                                >
                            @endif

                            <div class="flex-grow">
                                <a href="{{ $item->product ? route('products.show', $item->product->slug) : '#' }}"
                                   class="font-medium text-gray-800 hover:text-indigo-600">
                                    {{ $item->product_name }}
                                </a>
                                @if($item->variant)
                                    <p class="text-xs text-gray-500">
                                        @foreach($item->variant->options as $optionValue)
                                            {{ $optionValue->attribute->name }}: {{ $optionValue->value }}@if(!$loop->last), @endif
                                        @endforeach
                                    </p>
                                @endif
                                <p class="text-sm text-gray-600">Số lượng: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">{{ number_format($item->price, 0, ',', '.') }} VNĐ</p>
                                <p class="text-sm text-gray-500">Tổng: {{ number_format($item->subtotal, 0, ',', '.') }} VNĐ</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 border-t">
                    <div class="md:col-span-2"></div>
                    <div class="space-y-2 text-right">
                        <p><strong>Tạm tính:</strong> {{ number_format($order->subtotal, 0, ',', '.') }} VNĐ</p>
                        <p><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee, 0, ',', '.') }} VNĐ</p>
                        @if($order->discount_amount > 0)
                            <p class="text-red-600"><strong>Giảm giá:</strong> −{{ number_format($order->discount_amount, 0, ',', '.') }} VNĐ</p>
                        @endif
                        @if($order->tax_amount > 0)
                            <p><strong>Thuế:</strong> {{ number_format($order->tax_amount, 0, ',', '.') }} VNĐ</p>
                        @endif
                        <p class="text-xl font-bold"><strong>Tổng cộng:</strong> {{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</p>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-6 pt-6 border-t">
                        <h3 class="text-md font-semibold mb-2">Ghi Chú Của Khách Hàng:</h3>
                        <p class="text-gray-700">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection
