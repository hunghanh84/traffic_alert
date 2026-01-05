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
        Schema::create('gui_thong_bao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thong_bao_id')->constrained('thong_bao')->onDelete('cascade');
            $table->foreignId('nguoi_dung_id')->nullable()->constrained('nguoi_dung')->onDelete('cascade');
            $table->dateTime('thoi_gian_gui')->nullable();
            $table->string('kenh_gui', 50)->default('app'); // app / email / firebase
            $table->string('trang_thai', 50)->default('pending');
            $table->text('loi')->nullable();
            $table->timestamps(); // tao_luc, cap_nhat_luc
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gui_thong_bao');
    }
};
