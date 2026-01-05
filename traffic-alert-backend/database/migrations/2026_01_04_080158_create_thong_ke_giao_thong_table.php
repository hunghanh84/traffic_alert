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
        Schema::create('thong_ke_giao_thong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duong_id')->nullable()->constrained('duong')->onDelete('cascade');
            $table->foreignId('khu_vuc_id')->nullable()->constrained('khu_vuc')->onDelete('cascade');
            $table->string('loai_thoi_gian', 50); // ngay / tuan / thang
            $table->dateTime('bat_dau');
            $table->dateTime('ket_thuc');
            $table->integer('so_lan_tac_duong')->default(0);
            $table->integer('so_lan_ngap')->default(0);
            $table->double('muc_do_tac_duong_tb')->default(0);
            $table->double('muc_do_ngap_tb')->default(0);
            $table->text('tong_quan')->nullable();
            $table->timestamps(); // tao_luc, cap_nhat_luc
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_ke_giao_thong');
    }
};
