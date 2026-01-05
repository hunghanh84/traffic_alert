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
        Schema::create('duong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phuong_id')->constrained('phuong_xa')->onDelete('cascade');
            $table->foreignId('khu_vuc_id')->nullable()->constrained('khu_vuc')->onDelete('set null');
            $table->string('ten', 255);
            $table->string('ma', 50)->nullable();
            $table->string('loai_duong', 50)->nullable()->comment('Đường chính / Đường phụ / Đường nhánh / Đường nội bộ');
            $table->boolean('kich_hoat')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duong');
    }
};
