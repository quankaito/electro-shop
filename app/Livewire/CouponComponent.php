<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Promotion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CouponComponent extends Component
{
    public $couponCode = '';
    public $appliedPromotion = null;
    public $discountAmount = 0;
    public $cartTotal = 0;

    protected $rules = [
        'couponCode' => 'required|string',
    ];

    protected $messages = [
        'couponCode.required' => 'Vui lòng nhập mã giảm giá.',
    ];

    public function mount()
    {
        // Lấy tổng đơn hàng từ session (bạn phải set session('cart_total') trước khi render checkout)
        $this->cartTotal = session('cart_total', 0);

        // Nếu đã có promotion trong session, nạp lại
        if (session()->has('promotion')) {
            $promoData = session('promotion');
            $this->appliedPromotion = Promotion::find($promoData['id']);
            $this->discountAmount   = $promoData['discount_amount'];
            $this->couponCode       = $promoData['code'];
        }
    }

    public function applyCoupon()
    {
        $this->validate();

        $promotion = Promotion::where('code', $this->couponCode)->first();
        if (! $promotion) {
            throw ValidationException::withMessages([
                'couponCode' => 'Mã giảm giá không tồn tại.',
            ]);
        }

        if (! $promotion->is_active) {
            throw ValidationException::withMessages([
                'couponCode' => 'Mã giảm giá hiện không khả dụng.',
            ]);
        }

        $now = Carbon::now();
        if ($now->lt($promotion->start_date)) {
            throw ValidationException::withMessages([
                'couponCode' => 'Mã giảm giá chưa đến thời gian áp dụng.',
            ]);
        }
        if ($promotion->end_date && $now->gt($promotion->end_date)) {
            throw ValidationException::withMessages([
                'couponCode' => 'Mã giảm giá đã hết hạn.',
            ]);
        }

        $cartTotal = $this->cartTotal;
        if ($promotion->min_order_value !== null && $cartTotal < $promotion->min_order_value) {
            $formatted = number_format($promotion->min_order_value, 0, ',', '.');
            throw ValidationException::withMessages([
                'couponCode' => "Đơn hàng phải ≥ {$formatted} VNĐ để dùng mã này.",
            ]);
        }

        if ($promotion->usage_limit_per_code !== null && $promotion->times_used >= $promotion->usage_limit_per_code) {
            throw ValidationException::withMessages([
                'couponCode' => 'Mã giảm giá đã đạt giới hạn sử dụng.',
            ]);
        }

        // Tính discount
        $discount = 0;
        if ($promotion->type === 'fixed_amount') {
            $discount = floatval($promotion->value);
        } elseif ($promotion->type === 'percentage') {
            $raw = ($cartTotal * floatval($promotion->value)) / 100;
            if ($promotion->max_discount_amount !== null) {
                $discount = min($raw, floatval($promotion->max_discount_amount));
            } else {
                $discount = $raw;
            }
        }
        $discount = min($discount, $cartTotal);

        // Lưu vào session
        session()->put('promotion', [
            'id'              => $promotion->id,
            'code'            => $promotion->code,
            'type'            => $promotion->type,
            'value'           => floatval($promotion->value),
            'discount_amount' => $discount,
        ]);

        // Tăng times_used ngay (hoặc bạn có thể defer khi order thành công)
        $promotion->increment('times_used');

        // Cập nhật lại biến để view re-render
        $this->appliedPromotion = $promotion;
        $this->discountAmount   = $discount;

        session()->flash('success', 'Áp dụng mã giảm giá thành công.');
    }

    public function removeCoupon()
    {
        session()->forget('promotion');
        $this->appliedPromotion = null;
        $this->discountAmount   = 0;
        $this->couponCode       = '';
        session()->flash('success', 'Đã hủy mã giảm giá.');
    }

    public function render()
    {
        return view('livewire.coupon-component');
    }
}
