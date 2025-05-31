<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Phần hiển thị danh sách sản phẩm chính
        // Phần lọc và tìm kiếm sẽ được xử lý bởi Livewire component nhúng vào view này
        $products = Product::where('is_active', true);

        // Có thể có một số lọc cơ bản từ query string nếu cần (ví dụ: ?category=slug)
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $products->where('category_id', $category->id);
            }
        }
        // Sẽ có một Livewire component `ProductList` đảm nhận việc lọc và hiển thị chi tiết
        return view('frontend.products.index');
    }

    public function show(Product $product) // Route Model Binding
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['category', 'brand', 'images', 'variants.options.attribute', 'reviews.user']);
        $product->increment('views_count'); // Tăng lượt xem

        $relatedProducts = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }

    public function showByCategory(Category $category)
    {
        // Tương tự như index, nhưng có thể truyền category vào Livewire component
        if (!$category->is_active) {
            abort(404);
        }
        return view('frontend.products.category', compact('category'));
    }
}