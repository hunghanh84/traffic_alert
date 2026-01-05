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
        Schema::create('bai_dang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->nullable()->constrained('nguoi_dung')->onDelete('cascade');
            $table->foreignId('khu_vuc_id')->constrained('khu_vuc')->onDelete('cascade');
            $table->foreignId('duong_id')->constrained('duong')->onDelete('cascade');
            $table->string('loai_canh_bao', 50); // traffic, flood
            $table->string('muc_do', 50); // low, medium, high, critical
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_dang');
    }
};
