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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id(); // Hoặc bigIncrements nếu mã tỉnh là số lớn
            // $table->string('code', 10)->primary(); // Nếu bạn muốn dùng mã tỉnh làm PK
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->string('country_code', 2)->default('VN');
            // không cần timestamps cho dữ liệu này nếu nó là tĩnh
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
