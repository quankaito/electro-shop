<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Electro Shop') }} - @yield('title', 'Welcome')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite('resources/css/app.css')
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col">
        @include('frontend.partials.header')

        <main class="flex-grow">
            @yield('content')
            @isset($slot)
                {{ $slot }}
            @endisset
        </main>

        @include('frontend.partials.footer')
    </div>

    <!-- JS: Load app.js TRƯỚC livewireScripts -->
    @vite('resources/js/app.js')

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Extra Scripts -->
    <x-toast-notification />
    @stack('scripts')
    <!-- <x-chat-widget /> -->
    @auth
    @php
        // Lấy thông tin cuộc hội thoại của user đang đăng nhập ngay tại đây
        $conversation = \App\Models\Conversation::firstOrCreate(['user_id' => auth()->id()]);
    @endphp

    {{-- Truyền thẳng đối tượng conversation vào component --}}
    <x-chat-widget :conversation="$conversation" />
@endauth
</body>
</html>
