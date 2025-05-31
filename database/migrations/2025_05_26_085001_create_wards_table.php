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
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            // $table->string('code', 10)->primary();
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            // Nếu district_id là string (code) thì:
            // $table->string('district_code', 10);
            // $table->foreign('district_code')->references('code')->on('districts')->onDelete('cascade');
            $table->string('name');
            $table->string('code', 10)->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
