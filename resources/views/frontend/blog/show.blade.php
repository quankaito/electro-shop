{{-- resources/views/frontend/blog/show.blade.php --}}
@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <article class="bg-white p-6 md:p-8 rounded-lg shadow-md">
            @if($post->featured_image)
                <img 
                    src="{{ cloudinary_url($post->featured_image) }}" 
                    alt="{{ $post->title }}" 
                    class="w-full h-auto max-h-[500px] object-cover rounded-lg mb-6"
                >
            @endif

            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">{{ $post->title }}</h1>

            <div class="text-sm text-gray-500 mb-6 border-b pb-4">
                <span>Đăng bởi <strong class="text-gray-700">{{ $post->author->name }}</strong></span> |
                <span>{{ $post->published_at ? $post->published_at->format('d F, Y') : $post->created_at->format('d F, Y') }}</span> |
                @if($post->categories->isNotEmpty())
                    <span>Chuyên mục:
                        @foreach($post->categories as $category)
                            <a 
                                href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                                class="text-indigo-500 hover:underline"
                            >
                                {{ $category->name }}
                            </a>@if(!$loop->last), @endif
                        @endforeach
                    </span>
                @endif
            </div>

            <div class="prose lg:prose-lg max-w-none text-gray-700 leading-relaxed">
                {!! $post->content !!}
            </div>

            {{-- Author Bio --}}
            <div class="mt-10 pt-6 border-t flex items-center">
                @if($post->author->avatar)
                    <img 
                        src="{{ cloudinary_url($post->author->avatar) }}" 
                        alt="{{ $post->author->name }}" 
                        class="w-16 h-16 rounded-full mr-4 object-cover"
                    >
                @else
                    <span class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-300 text-gray-700 mr-4 text-xl">
                        {{ strtoupper(substr($post->author->name, 0, 1)) }}
                    </span>
                @endif
                <div>
                    <p class="font-semibold text-gray-800">{{ $post->author->name }}</p>
                    <p class="text-sm text-gray-600">Thông tin thêm về tác giả...</p>
                </div>
            </div>

            {{-- Social Share Buttons --}}
            <div class="mt-8 pt-6 border-t text-center">
                <h3 class="text-md font-semibold mb-3">Chia sẻ bài viết này:</h3>
                <div class="flex justify-center space-x-3">
                    {{-- Ví dụ chia sẻ Facebook/Twitter --}}
                    {{-- 
                    <a 
                        href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                        target="_blank" class="text-blue-600 hover:text-blue-800"
                    >
                        Facebook
                    </a>
                    <a 
                        href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" 
                        target="_blank" class="text-sky-500 hover:text-sky-700"
                    >
                        Twitter
                    </a>
                    --}}
                </div>
            </div>
        </article>

        {{-- Related Posts --}}
        @if($relatedPosts->isNotEmpty())
        <section class="mt-12">
            <h2 class="text-2xl font-semibold mb-6">Bài Viết Liên Quan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($relatedPosts as $relatedPost)
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        @if($relatedPost->featured_image)
                            <a href="{{ route('blog.show', $relatedPost->slug) }}">
                                <img 
                                    src="{{ cloudinary_url($relatedPost->featured_image) }}" 
                                    alt="{{ $relatedPost->title }}" 
                                    class="w-full h-40 object-cover rounded-t-lg mb-3"
                                >
                            </a>
                        @endif
                        <h3 class="text-lg font-semibold mb-1">
                            <a 
                                href="{{ route('blog.show', $relatedPost->slug) }}" 
                                class="text-gray-800 hover:text-indigo-600"
                            >
                                {{ $relatedPost->title }}
                            </a>
                        </h3>
                        <p class="text-xs text-gray-500 mb-2">
                            {{ $relatedPost->published_at 
                                ? $relatedPost->published_at->format('d/m/Y') 
                                : '' 
                            }}
                        </p>
                        <p class="text-sm text-gray-600 leading-snug">
                            {{ Str::limit(strip_tags($relatedPost->excerpt ?: $relatedPost->content), 100) }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Comments Section --}}
        <section class="mt-12" id="comments">
            <h2 class="text-2xl font-semibold mb-6">Bình Luận</h2>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <p class="text-gray-600">Hệ thống bình luận sẽ được tích hợp ở đây.</p>
            </div>
        </section>
    </div>
</div>
@endsection
