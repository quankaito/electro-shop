@extends('layouts.app')

@section('title', 'Câu Hỏi Thường Gặp')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold text-center mb-10">Câu Hỏi Thường Gặp (FAQ)</h1>

    @if($faqs->isNotEmpty())
        <div class="space-y-6 max-w-3xl mx-auto">
            @foreach($faqs as $faq)
                <div x-data="{ open: false }" class="border rounded-lg shadow-sm">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-4 text-left focus:outline-none">
                        <h2 class="text-lg font-semibold">{{ $faq->question }}</h2>
                        <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="p-4 border-t">
                        <div class="prose max-w-none">
                            {!! $faq->answer !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-center text-gray-600">Hiện chưa có câu hỏi thường gặp nào.</p>
    @endif
</div>
@endsection

@push('scripts')
    <!-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script> -->
@endpush