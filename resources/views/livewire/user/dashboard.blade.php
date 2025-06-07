<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-8">
        👋 Xin chào, {{ $user->name }}!
    </h1>

    {{-- Card tổng chi tiêu --}}
    <div class="bg-gradient-to-r from-green-400 to-emerald-500 text-white rounded-lg shadow-md p-6 mb-10">
        <h2 class="text-lg sm:text-xl font-semibold">Tổng chi tiêu của bạn:</h2>
        <p class="mt-2 text-4xl font-bold">{{ number_format($totalSpent, 0, ',', '.') }}₫</p>
    </div>

    {{-- Bảng lịch sử đơn hàng --}}
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">🛒 Lịch sử đơn hàng</h2>

        @if($orders->isEmpty())
            <p class="text-gray-500">Bạn chưa có đơn hàng nào.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Mã Đơn</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Thời gian</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Tổng tiền</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-800">{{ $order->order_number }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-right text-green-600 font-medium">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                </td>
                                <td class="px-4 py-3 capitalize text-gray-600">{{ $order->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-right">
                <a href="{{ route('account.orders') }}"
                   class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Xem tất cả đơn hàng
                </a>
            </div>
        @endif
    </div>
</div>
