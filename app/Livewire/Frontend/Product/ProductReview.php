<?php

namespace App\Livewire\Frontend\Product;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProductReview extends Component
{
    use WithPagination;

    public Product $product;
    public $rating = 0;
    public $comment = '';
    public $canReview = false;
    public $hasReviewed = false;

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        if (Auth::check()) {
            // Kiểm tra xem user đã mua sản phẩm này chưa (logic phức tạp hơn)
            // $this->canReview = Auth::user()->orders()->whereHas('items', fn($q) => $q->where('product_id', $this->product->id))->exists();
            $this->canReview = true; // Tạm thời cho phép review

            $this->hasReviewed = Review::where('user_id', Auth::id())
                                      ->where('product_id', $this->product->id)
                                      ->exists();
            if($this->hasReviewed){
                $existingReview = Review::where('user_id', Auth::id())
                                      ->where('product_id', $this->product->id)
                                      ->first();
                $this->rating = $existingReview->rating;
                $this->comment = $existingReview->comment;
            }
        }
    }

    public function submitReview()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->validate();

        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $this->product->id],
            ['rating' => $this->rating, 'comment' => $this->comment, 'is_approved' => false] // Chờ duyệt
        );

        $this->hasReviewed = true;
        $this->dispatch('showToast', message: 'Your review has been submitted and is awaiting approval.', type: 'success');
        // $this->reset(['rating', 'comment']); // Không reset nếu muốn cho sửa
    }

    public function setRating($rate)
    {
        $this->rating = $rate;
    }

    public function render()
    {
        $reviews = $this->product->reviews()
                          ->where('is_approved', true)
                          ->latest()
                          ->with('user') // Eager load user
                          ->paginate(5, ['*'], 'reviewsPage');

        return view('livewire.frontend.product.product-review', [
            'reviews' => $reviews,
        ]);
    }
}