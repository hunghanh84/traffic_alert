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
        Schema::create('thiet_lap_canh_bao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->foreignId('duong_id')->constrained('duong')->onDelete('cascade');
            $table->foreignId('muc_do_toi_thieu_id')->constrained('muc_do_su_kien')->onDelete('cascade')
                ->comment('Chỉ nhận cảnh báo từ mức độ này trở lên');
            $table->dateTime('thoi_gian_bat_dau')->nullable()->comment('Giờ bắt đầu nhận cảnh báo trong ngày');
            $table->dateTime('thoi_gian_ket_thuc')->nullable()->comment('Giờ kết thúc nhận cảnh báo trong ngày');
            $table->integer('thu_trong_tuan')->default(127)->comment('Bitmask: 1=Mon, 2=Tue, 4=Wed... 127=All days');
            $table->string('kenh_nhan', 50)->default('push')->comment('push / email / sms');
            $table->boolean('kich_hoat')->default(true)->comment('Bật/tắt cảnh báo');
            $table->timestamps();
            
            // Indexes
            $table->index('nguoi_dung_id');
            $table->index('duong_id');
            $table->index('kich_hoat');
            $table->index(['nguoi_dung_id', 'duong_id', 'kich_hoat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thiet_lap_canh_bao');
    }
};
