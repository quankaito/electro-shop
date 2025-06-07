<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            // Thêm cột để đánh dấu khuyến mãi nổi bật trên trang chủ
            $table->boolean('is_featured_on_home')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('is_featured_on_home');
        });
    }
};