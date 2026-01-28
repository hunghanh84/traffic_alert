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
        Schema::create('camera', function (Blueprint $table) {
            $table->id();
            $table->string('ma_camera', 100)->unique();
            $table->string('ten_camera', 255);
            $table->foreignId('duong_id')->nullable()->constrained('duong')->onDelete('cascade');
            $table->string('stream_url', 500)->nullable();
            $table->dateTime('lan_kiem_tra_cuoi')->nullable();
            $table->string('trang_thai_ket_noi', 50)->default('active');
            $table->integer('so_lan_kiem_tra')->default(0);
            $table->timestamps(); // tao_luc, cap_nhat_luc
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camera');
    }
};
