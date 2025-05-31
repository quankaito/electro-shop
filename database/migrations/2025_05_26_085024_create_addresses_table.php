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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('full_name');
            $table->string('phone_number', 15);
            $table->string('address_line1'); // Số nhà, đường
            $table->string('address_line2')->nullable(); // Thôn/ấp/tòa nhà
            $table->foreignId('ward_id')->nullable()->constrained('wards')->onDelete('set null');
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('set null');
            // Nếu dùng code cho địa chỉ:
            // $table->string('ward_code', 10)->nullable();
            // $table->foreign('ward_code')->references('code')->on('wards')->onDelete('set null');
            // $table->string('district_code', 10)->nullable();
            // $table->foreign('district_code')->references('code')->on('districts')->onDelete('set null');
            // $table->string('province_code', 10)->nullable();
            // $table->foreign('province_code')->references('code')->on('provinces')->onDelete('set null');
            $table->string('country_code', 2)->default('VN');
            $table->string('postal_code', 10)->nullable();
            $table->boolean('is_default_shipping')->default(false);
            $table->boolean('is_default_billing')->default(false);
            $table->enum('type', ['shipping', 'billing'])->nullable(); // Hoặc tách thành 2 cờ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
