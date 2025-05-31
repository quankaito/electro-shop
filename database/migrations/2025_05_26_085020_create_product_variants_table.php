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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('sku', 100)->nullable()->unique();
            // $table->decimal('price_modifier', 15, 2)->nullable(); // Thay đổi so với giá gốc
            $table->decimal('specific_price', 15, 2)->nullable(); // Giá cụ thể cho biến thể
            $table->integer('stock_quantity')->default(0);
            $table->foreignId('image_id')->nullable()->constrained('product_images')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
