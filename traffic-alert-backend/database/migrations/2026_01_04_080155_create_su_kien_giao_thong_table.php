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
        Schema::create('su_kien_giao_thong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ket_qua_ai_id')->nullable()->constrained('ket_qua_ai')->onDelete('set null');
            $table->foreignId('duong_id')->nullable()->constrained('duong')->onDelete('cascade');
            $table->foreignId('khu_vuc_id')->nullable()->constrained('khu_vuc')->onDelete('cascade');
            $table->foreignId('loai_su_kien_id')->nullable()->constrained('loai_su_kien')->onDelete('set null');
            $table->foreignId('muc_do_id')->nullable()->constrained('muc_do_su_kien')->onDelete('set null');
            $table->foreignId('trang_thai_id')->nullable()->constrained('trang_thai_su_kien')->onDelete('set null');
            $table->string('nguon', 50)->default('user'); // camera / user / he_thong
            $table->dateTime('bat_dau_luc')->nullable();
            $table->dateTime('ket_thuc_luc')->nullable();
            $table->text('mo_ta')->nullable();
            $table->timestamps(); // tao_luc, cap_nhat_luc
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('su_kien_giao_thong');
    }
};
