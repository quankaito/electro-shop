@extends('layouts.app')

@section('title', 'Đổi Mật Khẩu')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        @include('frontend.account.partials.sidebar')

        <main class="w-full md:w-3/4">
            <h1 class="text-2xl font-semibold mb-6">Đổi Mật Khẩu</h1>
            <div class="bg-white p-6 rounded-lg shadow max-w-lg">
                @if(session('success'))
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('account.password.update') }}" method="POST">
                    @csrf
                    @method('PUT') <!-- Hoặc PATCH -->

                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Mật Khẩu Hiện Tại</label>
                        <input type="password" name="current_password" id="current_password" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label for="new_password" class="block text-sm font-medium text-gray-700">Mật Khẩu Mới</label>
                        <input type="password" name="new_password" id="new_password" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="mb-6">
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Xác Nhận Mật Khẩu Mới</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cập Nhật Mật Khẩu
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection