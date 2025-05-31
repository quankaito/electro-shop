@extends('layouts.app')

@section('title', 'Sổ Địa Chỉ')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        @include('frontend.account.partials.sidebar')

        <main class="w-full md:w-3/4">
            {{-- @livewire('frontend.account.user-address-manager') --}}
            @livewire('frontend.account.user-address-manager')
            <!-- <div class="p-6 border-2 border-dashed border-gray-300 rounded-lg text-center">
                <p class="text-gray-500">Livewire Component "UserAddressManager" sẽ hiển thị ở đây để bạn quản lý các địa chỉ đã lưu.</p>
            </div> -->
        </main>
    </div>
</div>
@endsection