@extends('layouts.app')

@section('title', 'Thông Tin Cá Nhân')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        @include('frontend.account.partials.sidebar')

        <main class="w-full md:w-3/4">
            <h1 class="text-2xl font-semibold mb-6">Thông Tin Cá Nhân</h1>
            <div class="bg-white p-6 rounded-lg shadow">
                @livewire('frontend.account.user-profile-form')
                <!-- <div class="p-6 border-2 border-dashed border-gray-300 rounded-lg text-center">
                    <p class="text-gray-500">Livewire Component "UserProfileForm" sẽ hiển thị ở đây để bạn cập nhật thông tin.</p>
                </div> -->
            </div>

            <h2 class="text-xl font-semibold mt-8 mb-4">Đổi Mật Khẩu</h2>
             <div class="bg-white p-6 rounded-lg shadow">
                <a href="{{ route('account.password.change') }}" class="text-indigo-600 hover:underline">Nhấn vào đây để đổi mật khẩu của bạn.</a>
            </div>
        </main>
    </div>
</div>
@endsection