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
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id();
            $table->string('ten_dang_nhap', 100)->unique();
            $table->string('mat_khau', 255);
            $table->string('so_dien_thoai', 30)->nullable();
            $table->string('email', 255)->unique();
            $table->unsignedBigInteger('khu_vuc_id')->nullable();
            $table->enum('vai_tro', ['admin', 'nguoi_dung', 'dieu_hanh'])->default('nguoi_dung');
            $table->enum('trang_thai', ['hoat_dong', 'khoa', 'tam_dung'])->default('hoat_dong');
            $table->timestamps(); // tao_luc (created_at), cap_nhat_luc (updated_at)
            
            // Foreign key
            $table->foreign('khu_vuc_id')->references('id')->on('khu_vuc')->onDelete('set null');
            
            // Indexes
            $table->index('ten_dang_nhap');
            $table->index('email');
            $table->index('vai_tro');
            $table->index('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
