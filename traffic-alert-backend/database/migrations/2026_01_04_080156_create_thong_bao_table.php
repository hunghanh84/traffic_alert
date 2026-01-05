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
        Schema::create('thong_bao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('su_kien_id')->nullable()->constrained('su_kien_giao_thong')->onDelete('cascade');
            $table->string('tieu_de', 255);
            $table->text('noi_dung');
            $table->string('muc_do_uu_tien', 50)->default('normal');
            $table->string('trang_thai_gui', 50)->default('pending');
            $table->string('loai_thong_bao', 50)->default('system');
            $table->string('tao_boi', 50)->default('he_thong'); // he_thong / admin
            $table->timestamps(); // tao_luc, cap_nhat_luc
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_bao');
    }
};
