@extends('layouts.app')

@section('title', 'Đặt Hàng Thành Công')

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
        <svg class="w-20 h-20 text-green-500 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Đặt Hàng Thành Công!</h1>
        <p class="text-gray-600 mb-2">Cảm ơn bạn đã mua hàng tại cửa hàng của chúng tôi.</p>
        @if(isset($orderNumber) && $orderNumber)
            <p class="text-gray-600 mb-6">Mã đơn hàng của bạn là: <strong class="text-indigo-600">{{ $orderNumber }}</strong></p>
            <p class="text-gray-600 mb-6">Chúng tôi sẽ sớm liên hệ với bạn để xác nhận đơn hàng và thông tin giao hàng. Bạn cũng có thể theo dõi đơn hàng trong <a href="{{ route('account.orders') }}" class="text-indigo-600 hover:underline font-semibold">lịch sử mua hàng</a>.</p>
        @else
             <p class="text-gray-600 mb-6">Chúng tôi sẽ sớm xử lý đơn hàng của bạn. Bạn có thể theo dõi đơn hàng trong <a href="{{ route('account.orders') }}" class="text-indigo-600 hover:underline font-semibold">lịch sử mua hàng</a>.</p>
        @endif

        <div class="mt-8">
            <a href="{{ route('products.index') }}" class="inline-block px-8 py-3 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition duration-150">
                Tiếp Tục Mua Sắm
            </a>
        </div>
         <p class="mt-4 text-sm text-gray-500">Nếu có bất kỳ câu hỏi nào, vui lòng <a href="{{ route('contact') }}" class="text-indigo-500 hover:underline">liên hệ với chúng tôi</a>.</p>
    </div>
</div>
@endsection