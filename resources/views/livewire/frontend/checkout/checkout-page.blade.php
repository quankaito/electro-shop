{{-- resources/views/livewire/frontend/checkout/checkout-page.blade.php --}}
<div class="container mx-auto py-8">
    <form wire:submit.prevent="placeOrder">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT: Các bước (Steps) --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Step 1: Shipping Information --}}
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-1 flex items-center">
                        <span class="w-8 h-8 {{ $currentStep >= 1 ? 'bg-indigo-600' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center mr-3">
                            1
                        </span>
                        Thông Tin Giao Hàng
                    </h2>

                    @if($currentStep >= 1)
                        <div class="mt-4 space-y-4">

                            {{-- CHỌN ĐỊA CHỈ CŨ (Nếu có) --}}
                            @if($shippingAddresses->isNotEmpty())
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Chọn địa chỉ đã lưu</label>
                                    <select wire:model="selectedShippingAddressId"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Nhập địa chỉ mới bên dưới</option>
                                        @foreach($shippingAddresses as $addr)
                                            <option value="{{ $addr->id }}">
                                                {{ $addr->full_address }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="text-center text-sm text-gray-500">--- HOẶC ---</p>
                            @endif

                            {{-- NHẬP ĐỊA CHỈ MỚI --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Họ và Tên</label>
                                    <input
                                        type="text"
                                        wire:model.defer="newShippingAddress.full_name"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    />
                                    @error('newShippingAddress.full_name')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                                    <input
                                        type="text"
                                        wire:model.defer="newShippingAddress.phone_number"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    />
                                    @error('newShippingAddress.phone_number')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Địa chỉ cụ thể</label>
                                <input
                                    type="text"
                                    wire:model.defer="newShippingAddress.address_line1"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                />
                                @error('newShippingAddress.address_line1')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                {{-- Province --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tỉnh/Thành phố</label>
                                    <select
                                        wire:model="newShippingAddress.province_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    >
                                        <option value="">Chọn Tỉnh/Thành</option>
                                        @foreach($provinces as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newShippingAddress.province_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- District --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quận/Huyện</label>
                                    <select
                                        wire:model="newShippingAddress.district_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                        @disabled(empty($newShippingAddress['province_id']))
                                    >
                                        <option value="">Chọn Quận/Huyện</option>
                                        @foreach($districtsForShipping as $d)
                                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newShippingAddress.district_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Ward --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phường/Xã</label>
                                    <select
                                        wire:model.defer="newShippingAddress.ward_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                        @disabled(empty($newShippingAddress['district_id']))
                                    >
                                        <option value="">Chọn Phường/Xã</option>
                                        @foreach($wardsForShipping as $w)
                                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newShippingAddress.ward_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            @if($currentStep == 1)
                                <button
                                    type="button"
                                    wire:click="nextStep"
                                    wire:loading.attr="disabled"
                                    class="mt-4 w-full px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none"
                                >
                                    Tiếp Tục
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Step 2: Shipping Method --}}
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-1 flex items-center">
                        <span class="w-8 h-8 {{ $currentStep >= 2 ? 'bg-indigo-600' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center mr-3">
                            2
                        </span>
                        Phương Thức Vận Chuyển
                    </h2>

                    @if($currentStep >= 2)
                        <div class="mt-4 space-y-3">
                            @forelse($shippingMethods as $method)
                                <label class="flex items-center p-3 border rounded-md cursor-pointer
                                              {{ $selectedShippingMethodId == $method->id
                                                 ? 'border-indigo-600 bg-indigo-50 ring-2 ring-indigo-500'
                                                 : 'border-gray-300' }}">
                                    <input
                                        type="radio"
                                        wire:model.lazy="selectedShippingMethodId"
                                        name="shipping_method"
                                        value="{{ $method->id }}"
                                        class="form-radio h-5 w-5 text-indigo-600"
                                    />
                                    <div class="ml-3 flex-grow">
                                        <span class="block text-sm font-medium text-gray-800">
                                            {{ $method->name }}
                                        </span>
                                        @if($method->description)
                                            <p class="text-xs text-gray-500">{{ $method->description }}</p>
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">
                                        {{ number_format($method->cost, 0, ',', '.') }} VNĐ
                                    </span>
                                </label>
                            @empty
                                <p class="text-gray-500">Không có phương thức vận chuyển.</p>
                            @endforelse

                            @if($currentStep == 2)
                                <div class="flex justify-between mt-4">
                                    <button
                                        type="button"
                                        wire:click="previousStep"
                                        class="px-4 py-2 border rounded-md"
                                    >
                                        Quay Lại
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="nextStep"
                                        wire:loading.attr="disabled"
                                        class="px-6 py-3 bg-indigo-600 text-white rounded-md"
                                    >
                                        Tiếp Tục
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Step 3: Payment Method --}}
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-1 flex items-center">
                        <span class="w-8 h-8 {{ $currentStep >= 3 ? 'bg-indigo-600' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center mr-3">
                            3
                        </span>
                        Phương Thức Thanh Toán
                    </h2>

                    @if($currentStep >= 3)
                        <div class="mt-4 space-y-3">
                            @forelse($paymentMethods as $method)
                                <label class="flex items-start justify-between p-3 border rounded-md cursor-pointer
                                            {{ $selectedPaymentMethodId == $method->id
                                               ? 'border-indigo-600 bg-indigo-50 ring-2 ring-indigo-500'
                                               : 'border-gray-300' }}">
                                    {{-- Radio + nội dung --}}
                                    <div class="flex items-start">
                                        <input
                                            type="radio"
                                            wire:model.lazy="selectedPaymentMethodId"
                                            name="payment_method"
                                            value="{{ $method->id }}"
                                            class="form-radio h-5 w-5 text-indigo-600 mt-1"
                                        />
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-800">
                                                {{ $method->name }}
                                            </span>
                                            @if($method->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ $method->description }}</p>
                                            @endif
                                            @if($selectedPaymentMethodId == $method->id && $method->instructions)
                                                <div class="mt-2 p-2 bg-gray-100 rounded text-xs text-gray-600">
                                                    {!! nl2br(e($method->instructions)) !!}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Logo bên phải chỉ khi có và tồn tại --}}
                                    @if($method->logo && Storage::disk('cloudinary')->exists($method->logo))
                                        <div class="flex-shrink-0 ml-4">
                                            <img
                                                src="{{ cloudinary_url($method->logo) }}"
                                                alt="{{ $method->name }} logo"
                                                class="w-24 h-24 md:w-32 md:h-32 lg:w-48 lg:h-48 object-contain rounded-lg"
                                            />
                                        </div>
                                    @endif
                                </label>
                            @empty
                                <p class="text-gray-500">Không có phương thức thanh toán.</p>
                            @endforelse

                            @if($currentStep == 3)
                                <div class="flex justify-between mt-6">
                                    <button
                                        type="button"
                                        wire:click="previousStep"
                                        class="px-4 py-2 border rounded-md"
                                    >
                                        Quay Lại
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

            </div>

            {{-- RIGHT: Order Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md sticky top-8">
                    <h2 class="text-xl font-semibold mb-4">Tóm Tắt Đơn Hàng</h2>

                    {{-- Hiển thị danh sách item trong giỏ --}}
                    <div class="max-h-60 overflow-y-auto space-y-3 mb-4 pr-2 -mr-2 custom-scrollbar">
                        @foreach(Cart::content() as $item)
                            @php
                                $prod = $item->options->has('product_id_original')
                                      ? \App\Models\Product::find($item->options->product_id_original)
                                      : null;
                                if ($prod && $prod->images->isNotEmpty()) {
                                    $firstImg = $prod->images->firstWhere('is_thumbnail', true)?->image_path
                                              ?: $prod->images->first()->image_path;
                                    $img = cloudinary_url($firstImg);
                                } else {
                                    $img = 'https://via.placeholder.com/64?text=NoImg';
                                }
                            @endphp

                            <div class="flex items-start space-x-3">
                                <img src="{{ $img }}" class="w-16 h-16 rounded object-cover">
                                <div class="flex-grow">
                                    <p class="text-sm font-medium text-gray-800">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-500">SL: {{ $item->qty }}</p>
                                </div>
                                <span class="text-sm text-gray-700">
                                    {{ number_format($item->subtotal, 0, ',', '.') }} VNĐ
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Coupon: Nhập mã & Áp dụng / Xóa --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Mã giảm giá</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input
                                type="text"
                                wire:model.defer="couponCode"
                                placeholder="Nhập mã"
                                class="flex-1 border border-gray-300 rounded-l-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                            <button
                                type="button"
                                wire:click="applyCoupon"
                                wire:loading.attr="disabled"
                                wire:target="applyCoupon" {{-- Thêm target để spinner chỉ hiện khi bấm nút này --}}
                                class="relative px-4 bg-indigo-600 text-white border border-l-0 rounded-r-md hover:bg-indigo-700 focus:outline-none"
                            >
                                <span wire:loading.remove wire:target="applyCoupon">Áp dụng</span>
                                <span wire:loading wire:target="applyCoupon">Đang...</span>
                            </button>
                        </div>
                        @error('couponCode')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        @if($appliedPromotion)
                            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded flex justify-between items-center">
                                <div class="text-sm text-green-800">
                                    ✨ Áp dụng mã <span class="font-semibold">{{ $appliedPromotion->code }}</span>: −{{ number_format($discountAmount, 0, ',', '.') }} VNĐ
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeCoupon"
                                    wire:loading.attr="disabled"
                                    class="text-red-500 text-sm hover:underline focus:outline-none"
                                >× Xóa</button>
                            </div>
                        @endif
                    </div>
                    {{-- ========================================================= --}}
                    {{-- PHẦN MÃ MỚI: HIỂN THỊ CÁC VOUCHER GỢI Ý (Bản cập nhật)  --}}
                    {{-- ========================================================= --}}
                    @if($availablePromotions && $availablePromotions->isNotEmpty() && !$appliedPromotion)
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-800 mb-2">✨ Chọn một mã giảm giá có sẵn:</p>
                        <div class="space-y-3">
                            @foreach($availablePromotions as $promo)
                                <div class="p-3 bg-yellow-50 border border-dashed border-yellow-400 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center">
                                        {{-- Icon Voucher --}}
                                        <div class="flex-shrink-0 mr-3">
                                            <div class="w-10 h-10 bg-yellow-400 text-white rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 002 2h3m0 0h3m-3 0a2 2 0 00-2-2V7a2 2 0 002-2h3a2 2 0 002-2V5a2 2 0 00-2-2H5z" />
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        {{-- Thông tin voucher --}}
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $promo->name }}</p>
                                            <div class="text-xs text-gray-600 space-y-0.5">
                                                {{-- Dòng 1: Mô tả giảm giá và điều kiện tối thiểu --}}
                                                <p>
                                                    @if($promo->type == 'percentage')
                                                        Giảm {{ (int)$promo->value }}%
                                                        @if($promo->max_discount_amount)
                                                            (tối đa {{ number_format($promo->max_discount_amount, 0, ',', '.') }}đ).
                                                        @endif
                                                    @else
                                                        Giảm {{ number_format($promo->value, 0, ',', '.') }}đ.
                                                    @endif
                                                    @if($promo->min_order_value > 0)
                                                        Cho đơn từ {{ number_format($promo->min_order_value, 0, ',', '.') }}đ.
                                                    @endif
                                                </p>
                                                {{-- Dòng 2: Hạn sử dụng và Lượt dùng còn lại --}}
                                                <p class="flex items-center space-x-2">
                                                    @if($promo->end_date)
                                                        <span> HSD: {{ $promo->end_date->format('d/m/Y') }}</span>
                                                    @endif
                                                    
                                                    @if($promo->usage_limit_per_code > 0)
                                                        @if($promo->end_date) <span class="text-gray-300">|</span> @endif
                                                        <span> Còn {{ $promo->usage_limit_per_code - $promo->times_used }} lượt</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Nút Áp dụng --}}
                                    <button
                                        type="button"
                                        wire:click="applySuggestedCoupon('{{ $promo->code }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="applySuggestedCoupon('{{ $promo->code }}')"
                                        class="ml-4 px-3 py-1 text-sm font-medium text-indigo-700 bg-indigo-100 rounded-full hover:bg-indigo-200 focus:outline-none flex-shrink-0"
                                    >
                                        Áp dụng
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Totals --}}
                    <div class="mt-6 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Tạm tính:</span>
                            <span class="font-medium">{{ number_format($subtotal, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Thuế:</span>
                            <span class="font-medium">{{ number_format($taxAmount, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Phí vận chuyển:</span>
                            <span class="font-medium">{{ number_format($shippingFee, 0, ',', '.') }} VNĐ</span>
                        </div>
                        @if($discountAmount > 0)
                            <div class="flex justify-between text-sm text-green-700">
                                <span>Giảm giá:</span>
                                <span class="font-medium">−{{ number_format($discountAmount, 0, ',', '.') }} VNĐ</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center font-bold text-lg border-t pt-2">
                            <span>Tổng cộng:</span>
                            <span>{{ number_format($total, 0, ',', '.') }} VNĐ</span>
                        </div>
                    </div>

                    {{-- Nút Đặt Hàng Ngay --}}
                    @if($currentStep >= 3)
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="mt-6 w-full px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            <span wire:loading.remove>Đặt Hàng Ngay</span>
                            <span wire:loading>Đang xử lý...</span>
                        </button>
                        <p class="mt-2 text-xs text-gray-500 text-center">
                            Bằng việc đặt hàng, bạn đồng ý với
                            <a href="#" class="underline">Điều khoản &amp; Điều kiện</a>
                        </p>
                    @endif

                </div>
            </div>

        </div>
    </form>
</div>
