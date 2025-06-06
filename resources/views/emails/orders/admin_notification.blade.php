@component('mail::message')
# Thông báo đơn hàng mới

Có đơn hàng mới vừa được đặt trên hệ thống.

- **Mã đơn hàng:** #{{ $order->order_number }}  
- **Khách hàng:** {{ $order->customer_name }} ({{ $order->customer_email }} / {{ $order->customer_phone }})  
- **Tổng cộng (total):** {{ number_format($order->total_amount, 0, ',', '.') }}₫  

**Chi tiết tóm tắt đơn hàng:**

| Sản phẩm        | Số lượng | Giá  |
| --------------- | :------: | ---: |
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | {{ number_format($item->subtotal, 0, ',', '.') }}₫ |
@endforeach

Trân trọng,  
{{ config('app.name') }}

@endcomponent
