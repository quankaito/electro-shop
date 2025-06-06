{{-- resources/views/layouts/app.blade.php --}}
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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles') {{-- Cho các style cụ thể của trang --}}
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        @include('frontend.partials.header')

        <!-- Page Content -->
        <main class="flex-grow">
            {{-- Nội dung của các view Blade thông thường --}}
            @yield('content')

            {{-- Nếu có Livewire component, Livewire sẽ gán vào biến $slot --}}
            @isset($slot)
                {{ $slot }}
            @endisset
        </main>

        <!-- Footer -->
        @include('frontend.partials.footer')
    </div>

    @livewireScripts
    <x-toast-notification /> {{-- Component Toast từ Alpine.js --}}
    @stack('scripts') {{-- Cho các script cụ thể của trang --}}
</body>
</html>
