<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;     // Thêm import Category
use App\Models\Faq;
use App\Models\Post;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Brand;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $newProducts = Product::where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        $homeBanners = Banner::where('is_active', true)
            ->where('position', 'homepage_slider') // Ví dụ
            ->orderBy('sort_order')
            ->get();

        // Lấy danh sách category gốc để hiển thị “Khám Phá Danh Mục”
        $categoriesForHome = Category::where('is_active', true)
                                     ->whereNull('parent_id')
                                     ->orderBy('name')
                                     ->get();

        // === PHẦN MÃ MỚI ===
        // Lấy khuyến mãi được đánh dấu nổi bật, còn hạn và đang hoạt động
        $featuredPromotion = Promotion::where('is_featured_on_home', true)
                                      ->where('is_active', true)
                                      ->where('start_date', '<=', now())
                                      ->where('end_date', '>=', now()) // Phải có ngày kết thúc để đếm ngược
                                      ->first(); // Chỉ lấy 1 cái         
                                      // === PHẦN MÃ MỚI: LẤY THƯƠNG HIỆU ===
        $brandsForHome = Brand::where('is_active', true)
                                ->whereNotNull('logo') // Chỉ lấy các brand có logo cho đẹp
                                ->take(4) // Lấy tối đa 4 brand
                                ->get();
        // Truyền thêm $categoriesForHome vào view
        return view('frontend.home', compact(
            'featuredProducts',
            'newProducts',
            'homeBanners',
            'categoriesForHome',
            'featuredPromotion',
            'brandsForHome'
        ));
    }

    public function contact()
    {
        return view('frontend.pages.contact');
    }

    public function handleContactForm(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Xử lý gửi email hoặc lưu vào DB
        // Mail::to(config('mail.from.address'))->send(new ContactFormMail($request->all()));

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function faq()
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get();
        return view('frontend.pages.faq', compact('faqs'));
    }

    public function about()
    {
        return view('frontend.pages.about');
    }

    // Các trang tĩnh khác (privacy policy, terms, etc.)
}
