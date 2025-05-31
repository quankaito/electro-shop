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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Hoặc onDelete('set null') nếu muốn giữ lại item dù sản phẩm bị xóa
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->string('product_name'); // Lưu lại tên sản phẩm tại thời điểm mua
            $table->integer('quantity');
            $table->decimal('price', 15, 2); // Giá một sản phẩm tại thời điểm mua
            $table->decimal('subtotal', 15, 2); // quantity * price
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
