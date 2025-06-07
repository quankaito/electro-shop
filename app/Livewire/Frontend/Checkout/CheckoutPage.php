<?php

namespace App\Livewire\Frontend\Checkout;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Promotion;
use App\Models\ShippingMethod;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Mail\NewOrderAdminMail;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Log; // Để log lỗi nếu gửi mail thất bại
use Illuminate\Support\Facades\Mail;

class CheckoutPage extends Component
{
    // Thêm thuộc tính này vào đầu class CheckoutPage
    public $availablePromotions;
    // Thêm phương thức này vào trong class CheckoutPage
    public function applySuggestedCoupon($code)
    {
        $this->couponCode = $code;
        $this->applyCoupon();
    }
    public int $currentStep = 1;

    // Step 1: Shipping Info
    public $shippingAddresses;           // Danh sách địa chỉ cũ của user
    public $selectedShippingAddressId;   // ID của địa chỉ cũ đang chọn
    public array $newShippingAddress = [ // Dự trữ thông tin nhập mới hoặc preload từ địa chỉ cũ
        'full_name'     => '',
        'phone_number'  => '',
        'address_line1' => '',
        'address_line2' => '',
        'province_id'   => null,
        'district_id'   => null,
        'ward_id'       => null,
    ];

    // Step 2: Shipping Method
    public $shippingMethods;
    public $selectedShippingMethodId;
    public float $shippingFee = 0;

    // Step 3: Payment Method
    public $paymentMethods;
    public $selectedPaymentMethodId;

    // Coupon
    public string $couponCode           = '';
    public ?Promotion $appliedPromotion = null;
    public float $discountAmount        = 0;

    // Totals
    public float $subtotal  = 0;
    public float $taxAmount = 0;
    public float $total     = 0;

    protected $listeners = [
        'updatedSelectedShippingAddressId', // lắng nghe khi user chọn địa chỉ cũ
    ];

    public function mount()
    {
        // Nếu cart trống, redirect về cart
        if (Cart::count() === 0) {
            return redirect()->route('cart.index');
        }

        // Lấy danh sách address của user hiện tại
        $user = Auth::user();
        $this->shippingAddresses = $user->addresses()
                                       ->orderBy('is_default_shipping', 'desc')
                                       ->get();

        if ($this->shippingAddresses->isNotEmpty()) {
            // Thiết lập mặc định: chọn address có is_default_shipping=true nếu có, 
            // hoặc address đầu tiên
            $default = $this->shippingAddresses
                            ->firstWhere('is_default_shipping', true)
                    ?? $this->shippingAddresses->first();
            $this->selectedShippingAddressId = $default->id;

            // Preload thông tin province/district/ward từ địa chỉ cũ đó
            $this->preloadAddress($default);
        }

        // Nếu không có address cũ, selectedShippingAddressId sẽ là null 
        // và người dùng phải chọn “Nhập địa chỉ mới bên dưới” để fill

        // Prefill tên và phone của user vào newShippingAddress (dự cho trường hợp nhập mới)
        $this->newShippingAddress['full_name']    = $user->name;
        $this->newShippingAddress['phone_number'] = $user->phone_number;

        // Lấy shipping methods hiện có
        $this->shippingMethods = ShippingMethod::where('is_active', true)
                                               ->orderBy('sort_order')
                                               ->get();
        if ($this->shippingMethods->isNotEmpty()) {
            $this->selectedShippingMethodId = $this->shippingMethods->first()->id;
            $this->shippingFee              = $this->shippingMethods->first()->cost;
        }

        // Lấy payment methods hiện có
        $this->paymentMethods = PaymentMethod::where('is_active', true)
                                             ->orderBy('sort_order')
                                             ->get();
        if ($this->paymentMethods->isNotEmpty()) {
            $this->selectedPaymentMethodId = $this->paymentMethods->first()->id;
        }

        // Tính toán các giá trị subtotal, tax, total lần đầu
        $this->calculateTotals();
        // Tải các khuyến mãi có sẵn sau khi đã có subtotal
        $this->loadAvailablePromotions(); // <--- DÒNG MỚI
    }

    /**
     * Preload thông tin province_id, district_id, ward_id từ address cũ
     */
    protected function preloadAddress(Address $addr)
    {
        // Giả sử bảng addresses có cột province_id, district_id, ward_id
        $this->newShippingAddress['province_id'] = $addr->province_id;
        $this->newShippingAddress['district_id'] = $addr->district_id;
        $this->newShippingAddress['ward_id']     = $addr->ward_id;
    }

    /**
     * Listener: khi user chọn (hoặc bỏ chọn) địa chỉ cũ
     */
    public function updatedSelectedShippingAddressId($value)
    {
        if ($value) {
            // Nếu chọn một địa chỉ cũ, preload dữ liệu từ DB
            $addr = Address::find($value);
            if ($addr) {
                $this->preloadAddress($addr);
            }
        } else {
            // Nếu chọn “Nhập địa chỉ mới bên dưới” (value = ''), reset các field
            $this->newShippingAddress['province_id']  = null;
            $this->newShippingAddress['district_id']  = null;
            $this->newShippingAddress['ward_id']      = null;
            $this->newShippingAddress['address_line1']= '';
            // full_name và phone_number để sẵn, user có thể sửa nếu muốn
        }
    }

    /**
     * Tính các giá trị subtotal, tax, discount, total
     */
    public function calculateTotals()
    {
        // Lấy subtotal và tax từ Cart
        $rawSubtotal = (string) Cart::subtotal(2, '.', ''); // e.g. "100000.00"
        $rawTax      = (string) Cart::tax(2, '.', '');

        $this->subtotal  = (float) str_replace(',', '', $rawSubtotal);
        $this->taxAmount = (float) str_replace(',', '', $rawTax);

        // Cập nhật phí vận chuyển theo selectedShippingMethodId
        if ($this->selectedShippingMethodId) {
            $method = ShippingMethod::find($this->selectedShippingMethodId);
            $this->shippingFee = $method ? $method->cost : 0;
        } else {
            $this->shippingFee = 0;
        }

        // Tính discount nếu có appliedPromotion
        $this->discountAmount = 0;
        if ($this->appliedPromotion) {
            if ($this->appliedPromotion->type === 'fixed_amount') {
                $this->discountAmount = (float) $this->appliedPromotion->value;
            } else {
                // percentage
                $percent  = (float) $this->appliedPromotion->value;
                $discount = ($this->subtotal * $percent) / 100;
                if (
                    $this->appliedPromotion->max_discount_amount &&
                    $discount > $this->appliedPromotion->max_discount_amount
                ) {
                    $discount = $this->appliedPromotion->max_discount_amount;
                }
                $this->discountAmount = $discount;
            }
        }

        // Tính total
        $this->total = $this->subtotal + $this->taxAmount + $this->shippingFee - $this->discountAmount;
        if ($this->total < 0) {
            $this->total = 0;
        }
    }

    /**
     * Khi user thay đổi shipping method, update totals lại
     */
    public function updatedSelectedShippingMethodId()
    {
        $this->calculateTotals();
    }

    // Thêm phương thức này vào trong class CheckoutPage
    // Trong file CheckoutPage.php

    public function loadAvailablePromotions()
    {
        // Lấy subtotal hiện tại để so sánh
        $currentSubtotal = (float) str_replace(',', '', (string) Cart::subtotal(2, '.', ''));

        $this->availablePromotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                // Điều kiện: hoặc không có ngày hết hạn, hoặc ngày hết hạn trong tương lai
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where(function ($query) {
                // Điều kiện: hoặc không giới hạn lượt dùng, hoặc lượt dùng chưa hết
                $query->whereNull('usage_limit_per_code')
                    ->orWhereRaw('times_used < usage_limit_per_code');
            })
            // === PHẦN SỬA ĐỔI QUAN TRỌNG ===
            // Điều kiện: hoặc không yêu cầu giá trị tối thiểu, HOẶC giá trị tối thiểu phù hợp
            ->where(function ($query) use ($currentSubtotal) {
                $query->whereNull('min_order_value')
                    ->orWhere('min_order_value', '=', 0)
                    ->orWhere('min_order_value', '<=', $currentSubtotal);
            })
            ->orderBy('value', 'desc') // Ưu tiên các mã có giá trị giảm cao hơn
            ->get();
    }

    /**
     * Áp dụng coupon nhập vào
     */
    public function applyCoupon()
    {
        $this->validateOnly('couponCode', [
            'couponCode' => 'required|string',
        ]);

        $promo = Promotion::where('code', $this->couponCode)
                          ->where('is_active', true)
                          ->where('start_date', '<=', now())
                          ->where(fn($q) => $q->whereNull('end_date')
                                           ->orWhere('end_date', '>=', now()))
                          ->first();

        if (! $promo) {
            $this->addError('couponCode', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
            return;
        }

        if (
            $promo->min_order_value &&
            $this->subtotal < $promo->min_order_value
        ) {
            $min = number_format($promo->min_order_value, 0, ',', '.');
            $this->addError('couponCode', "Đơn hàng phải ≥ {$min} VNĐ để dùng mã này.");
            return;
        }

        $this->appliedPromotion = $promo;
        if ($promo->type === 'fixed_amount') {
            $this->discountAmount = (float) $promo->value;
        } else {
            $percent  = (float) $promo->value;
            $discount = ($this->subtotal * $percent) / 100;
            if ($promo->max_discount_amount && $discount > $promo->max_discount_amount) {
                $discount = $promo->max_discount_amount;
            }
            $this->discountAmount = $discount;
        }

        $this->calculateTotals();
        session()->flash('success', 'Áp dụng mã giảm giá thành công!');
    }

    /**
     * Hủy coupon
     */
    public function removeCoupon()
    {
        $this->appliedPromotion = null;
        $this->discountAmount   = 0;
        $this->couponCode       = '';
        $this->calculateTotals();

        session()->flash('success', 'Đã hủy mã giảm giá.');
    }

    /**
     * Next step (đi từ step 1 sang step 2, v.v.)
     */
    public function nextStep()
    {
        if ($this->currentStep === 1) {
            // Nếu user chưa chọn address cũ (selectedShippingAddressId == ''), validate nhập mới
            if (empty($this->selectedShippingAddressId)) {
                $this->validate([
                    'newShippingAddress.full_name'     => 'required|string',
                    'newShippingAddress.phone_number'  => 'required|string',
                    'newShippingAddress.address_line1' => 'required|string',
                    'newShippingAddress.province_id'   => 'required',
                    'newShippingAddress.district_id'   => 'required',
                    'newShippingAddress.ward_id'       => 'required',
                ]);

                // Tạo address mới trong DB
                $new = Auth::user()->addresses()->create([
                    'full_name'     => $this->newShippingAddress['full_name'],
                    'phone_number'  => $this->newShippingAddress['phone_number'],
                    'address_line1' => $this->newShippingAddress['address_line1'],
                    'address_line2' => $this->newShippingAddress['address_line2'] ?? '',
                    'province_id'   => $this->newShippingAddress['province_id'],
                    'district_id'   => $this->newShippingAddress['district_id'],
                    'ward_id'       => $this->newShippingAddress['ward_id'],
                ]);
                $this->selectedShippingAddressId = $new->id;
            }
        }

        $this->currentStep++;
        $this->calculateTotals();
    }

    public function previousStep()
    {
        $this->currentStep = max(1, $this->currentStep - 1);
    }

    /**
     * Tạo order khi final submit và giảm tồn kho
     */
    public function placeOrder()
    {
        if (! $this->selectedShippingAddressId) {
            session()->flash('error', 'Vui lòng chọn hoặc thêm địa chỉ giao hàng.');
            $this->currentStep = 1;
            return;
        }
        if (! $this->selectedShippingMethodId) {
            session()->flash('error', 'Vui lòng chọn phương thức vận chuyển.');
            $this->currentStep = 2;
            return;
        }
        if (! $this->selectedPaymentMethodId) {
            session()->flash('error', 'Vui lòng chọn phương thức thanh toán.');
            $this->currentStep = 3;
            return;
        }

        $orderNumber = null;
        $order = null; // Để truyền ra ngoài transaction

        DB::transaction(function () use (&$orderNumber, &$order) {
            $addr = Address::find($this->selectedShippingAddressId);

            // Tạo order
            $order = Order::create([
                'user_id'             => Auth::id(),
                'customer_name'       => $addr->full_name,
                'customer_email'      => Auth::user()->email,
                'customer_phone'      => $addr->phone_number,
                'shipping_address_id' => $this->selectedShippingAddressId,
                'billing_address_id'  => $this->selectedShippingAddressId,
                'shipping_method_id'  => $this->selectedShippingMethodId,
                'payment_method_id'   => $this->selectedPaymentMethodId,
                'subtotal'            => $this->subtotal,
                'tax_amount'          => $this->taxAmount,
                'shipping_fee'        => $this->shippingFee,
                'discount_amount'     => $this->discountAmount,
                'total_amount'        => $this->total,
                'status'              => 'pending',
                'notes'               => '',
            ]);

            // Duyệt các item trong Cart
            foreach (Cart::content() as $item) {
                $productIdOriginal = $item->options->product_id_original ?? null;
                $variantId         = $item->options->variant_id ?? null;

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $productIdOriginal,
                    'product_variant_id' => $variantId,
                    'product_name'       => $item->name,
                    'quantity'           => $item->qty,
                    'price'              => $item->price,
                    'subtotal'           => $item->subtotal,
                ]);

                // Giảm tồn kho
                if ($productIdOriginal) {
                    $product = Product::find($productIdOriginal);
                    if ($product) {
                        $product->decrement('stock_quantity', $item->qty);
                    }
                }
            }

            // Áp dụng Promotion nếu có
            if ($this->appliedPromotion) {
                $order->promotions()->attach($this->appliedPromotion->id, [
                    'discount_applied' => $this->discountAmount,
                ]);
                $this->appliedPromotion->increment('times_used');
            }

            // Xóa giỏ hàng
            Cart::destroy();

            // Lưu order_number vào session để hiển thị ở trang success
            session()->put('last_order_number', $order->order_number);
            $orderNumber = $order->order_number;
        });

        // --- Sau khi DB::transaction thành công, tiến hành gửi email ---
        if ($order) {
            // 1) Gửi email xác nhận cho khách hàng
            Mail::to($order->customer_email)
                ->send(new OrderConfirmationMail($order));

            // 2) Gửi email thông báo cho admin
            $adminEmail = config('mail.admin_address'); // hoặc env('ADMIN_EMAIL')
            if ($adminEmail) {
                Mail::to($adminEmail)
                    ->send(new NewOrderAdminMail($order));
            }
        }

        // Chuyển hướng đến trang success
        return redirect()->route('checkout.success', ['orderId' => $orderNumber]);
    }

    public function render()
    {
        // Danh sách provinces
        $provinces = \App\Models\Province::orderBy('name')->get();

        // Districts dựa vào province_id (preload hoặc nhập mới)
        $districtsForShipping = $this->newShippingAddress['province_id']
            ? \App\Models\District::where('province_id', $this->newShippingAddress['province_id'])
                                  ->orderBy('name')->get()
            : collect();

        // Wards dựa vào district_id
        $wardsForShipping = $this->newShippingAddress['district_id']
            ? \App\Models\Ward::where('district_id', $this->newShippingAddress['district_id'])
                              ->orderBy('name')->get()
            : collect();

        return view('livewire.frontend.checkout.checkout-page', compact(
            'provinces','districtsForShipping','wardsForShipping'
        ))->layout('layouts.app');
    }
}
