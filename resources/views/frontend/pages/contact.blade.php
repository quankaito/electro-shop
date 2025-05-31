@extends('layouts.app')

@section('title', 'Liên Hệ')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold text-center mb-8">Liên Hệ Với Chúng Tôi</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- Contact Form -->
        <div>
            <h2 class="text-2xl font-semibold mb-4">Gửi Tin Nhắn</h2>
            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Họ và Tên</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full px-3 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="mt-1 block w-full px-3 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-700">Tiêu Đề</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                           class="mt-1 block w-full px-3 py-2 border @error('subject') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-6">
                    <label for="message" class="block text-sm font-medium text-gray-700">Nội Dung Tin Nhắn</label>
                    <textarea name="message" id="message" rows="5" required
                              class="mt-1 block w-full px-3 py-2 border @error('message') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Gửi Tin Nhắn
                    </button>
                </div>
            </form>
        </div>

        <!-- Contact Information -->
        <div>
            <h2 class="text-2xl font-semibold mb-4">Thông Tin Liên Hệ</h2>
            <div class="space-y-3">
                <p><strong>Địa chỉ:</strong> 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
                <p><strong>Điện thoại:</strong> (028) 3456 7890</p>
                <p><strong>Email:</strong> support@ElectroShop.com.vn</p>
                <p><strong>Giờ làm việc:</strong> Thứ 2 - Thứ 7: 8:00 AM - 6:00 PM</p>
            </div>
            <div class="mt-6">
                {{-- Google Maps Embed --}}
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.447188203909!2d106.6296541758874!3d10.776999659244704!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317529dc1f951c87%3A0x8377d0e94e08ada!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBCw6FjaCBraG9hIC0gxJDhuqFpIGjhu41jIFF14buRYyBnaWEgVFAuSENN!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection