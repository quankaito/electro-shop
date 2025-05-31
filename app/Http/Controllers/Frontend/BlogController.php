<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::where('status', 'published')
            ->when($request->category, function ($query, $categorySlug) {
                $query->whereHas('categories', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->latest('published_at')
            ->paginate(10);

        $categories = PostCategory::withCount('posts')->orderBy('name')->get();
        return view('frontend.blog.index', compact('posts', 'categories'));
    }

    public function show(Post $post) // Route model binding
    {
        if ($post->status !== 'published' && (auth()->guest() || !auth()->user()->is_admin)) {
            abort(404); // Chỉ admin mới xem được draft/archived
        }
        $post->load('author', 'categories');
        $relatedPosts = Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            // ->whereHas('categories', fn($q) => $q->whereIn('id', $post->categories->pluck('id'))) // Cùng danh mục
            ->inRandomOrder()
            ->take(3)
            ->get();
        return view('frontend.blog.show', compact('post', 'relatedPosts'));
    }
}