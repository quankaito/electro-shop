@component('mail::message')
# Xin chào {{ $order->customer_name }},

Cảm ơn bạn đã đặt hàng tại {{ config('app.name') }}.  
Đơn hàng của bạn đã được ghi nhận với mã **#{{ $order->order_number }}**.

**Chi tiết đơn hàng:**

| Sản phẩm        | Số lượng | Giá  |
| --------------- | :------: | ---: |
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | {{ number_format($item->subtotal, 0, ',', '.') }}₫ |
@endforeach

- **Tổng phụ (subtotal):** {{ number_format($order->subtotal, 0, ',', '.') }}₫  
- **Thuế (tax):** {{ number_format($order->tax_amount, 0, ',', '.') }}₫  
- **Phí vận chuyển:** {{ number_format($order->shipping_fee, 0, ',', '.') }}₫  
- **Giảm giá:** {{ number_format($order->discount_amount, 0, ',', '.') }}₫  
- **Tổng cộng (total):** {{ number_format($order->total_amount, 0, ',', '.') }}₫  

**Địa chỉ giao hàng:**  
{{ $order->shippingAddress->address_line1 }},  
{{ $order->shippingAddress->ward->name }}, {{ $order->shippingAddress->district->name }}, {{ $order->shippingAddress->province->name }}.  

Nếu có bất kỳ thắc mắc nào, bạn có thể liên hệ với chúng tôi qua email: **nguyenminhquan170304@gmail.com**.

Cảm ơn bạn và hẹn gặp lại!  
{{ config('app.name') }}

@endcomponent
