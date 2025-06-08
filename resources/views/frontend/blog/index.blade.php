@extends('layouts.app')

@section('title', 'Tin Tức & Blog')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold text-center mb-10">Tin Tức & Bài Viết</h1>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Blog Posts -->
        <main class="w-full md:w-2/3 lg:w-3/4 space-y-8">
            @forelse($posts as $post)
                <article class="bg-white p-6 rounded-lg shadow-md">
                    @if($post->featured_image)
                        <a href="{{ route('blog.show', $post->slug) }}">
                            <img src="{{ cloudinary_url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded-t-lg mb-4">
                        </a>
                    @endif
                    <h2 class="text-2xl font-semibold mb-2">
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-gray-800 hover:text-indigo-600">{{ $post->title }}</a>
                    </h2>
                    <div class="text-sm text-gray-500 mb-3">
                        <span>Đăng bởi {{ $post->author->name }}</span> |
                        <span>{{ $post->published_at ? $post->published_at->format('d/m/Y') : $post->created_at->format('d/m/Y') }}</span> |
                        @if($post->categories->isNotEmpty())
                            <span>
                                @foreach($post->categories as $category)
                                    <a href="{{ route('blog.index', ['category' => $category->slug]) }}" class="text-indigo-500 hover:underline">{{ $category->name }}</a>@if(!$loop->last), @endif
                                @endforeach
                            </span>
                        @endif
                    </div>
                    <div class="text-gray-700 leading-relaxed">
                        {!! Str::limit(strip_tags($post->excerpt ?: $post->content), 250) !!}
                    </div>
                    <a href="{{ route('blog.show', $post->slug) }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800 font-semibold">Đọc thêm →</a>
                </article>
            @empty
                <p class="text-center text-gray-600 col-span-full">Không tìm thấy bài viết nào.</p>
            @endforelse

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </main>

        <!-- Blog Sidebar -->
        <aside class="w-full md:w-1/3 lg:w-1/4">
            <div class="bg-white p-6 rounded-lg shadow-md sticky top-8">
                <h3 class="text-xl font-semibold mb-4">Tìm Kiếm</h3>
                <form action="{{ route('blog.index') }}" method="GET">
                    <input type="text" name="search" placeholder="Tìm bài viết..." value="{{ request('search') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="mt-2 w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700">Tìm</button>
                </form>

                <h3 class="text-xl font-semibold mt-8 mb-4">Danh Mục</h3>
                @if($categories->isNotEmpty())
                <ul class="space-y-2">
                    <li><a href="{{ route('blog.index') }}" class="text-gray-700 hover:text-indigo-600 {{ !request('category') ? 'font-bold' : '' }}">Tất cả bài viết</a></li>
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                               class="text-gray-700 hover:text-indigo-600 {{ request('category') == $category->slug ? 'font-bold' : '' }}">
                                {{ $category->name }} ({{ $category->posts_count }})
                            </a>
                        </li>
                    @endforeach
                </ul>
                @else
                    <p class="text-sm text-gray-500">Không có danh mục nào.</p>
                @endif

                {{-- Có thể thêm Recent Posts, Tags Cloud, ... --}}
            </div>
        </aside>
    </div>
</div>
@endsection