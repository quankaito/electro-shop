<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Cho khách vãng lai
            $table->string('order_number', 50)->unique();
            $table->string('customer_name'); // Ngay cả khi user_id NULL hoặc khác user
            $table->string('customer_email');
            $table->string('customer_phone', 15);

            // Lưu thông tin địa chỉ trực tiếp hoặc tham chiếu. Tham chiếu tốt hơn.
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->onDelete('set null');
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->onDelete('set null');
            // Hoặc lưu text địa chỉ trực tiếp nếu không muốn phụ thuộc bảng addresses
            // $table->text('shipping_address_full')->nullable();
            // $table->text('billing_address_full')->nullable();

            $table->foreignId('shipping_method_id')->nullable()->constrained('shipping_methods')->onDelete('set null');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');

            $table->decimal('subtotal', 15, 2);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0); // Nếu có
            $table->decimal('total_amount', 15, 2);

            $table->enum('status', [
                'pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded', 'failed', 'payment_pending', 'confirmed'
            ])->default('pending');
            // Thêm 'confirmed' cho trạng thái đã xác nhận đơn hàng
            // Thêm 'payment_pending' cho các đơn hàng chờ thanh toán online

            $table->text('notes')->nullable(); // Ghi chú của khách
            $table->text('admin_notes')->nullable(); // Ghi chú của admin

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Cân nhắc soft delete cho đơn hàng
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
