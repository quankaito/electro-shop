<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-3xl font-semibold mb-6">Xin chào, {{ $user->name }}!</h1>

    {{-- Card tổng chi tiêu --}}
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h2 class="text-xl font-medium">Tổng chi tiêu của bạn:</h2>
        <p class="mt-2 text-3xl text-green-600">{{ number_format($totalSpent, 0, ',', '.') }}₫</p>
    </div>

    {{-- Bảng lịch sử đơn hàng --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-medium mb-4">Lịch sử đơn hàng</h2>
        @if($orders->isEmpty())
            <p>Chưa có đơn hàng nào.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã Đơn</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <tr>
                            <td class="px-4 py-2">{{ $order->order_number }}</td>
                            <td class="px-4 py-2">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                            <td class="px-4 py-2 capitalize">{{ $order->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4 text-right">
                <a href="{{ route('account.orders') }}" class="text-blue-600 hover:underline">
                    Xem tất cả đơn hàng
                </a>
            </div>
        @endif
    </div>
</div>
