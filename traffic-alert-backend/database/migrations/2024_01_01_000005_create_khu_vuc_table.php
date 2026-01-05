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
        Schema::create('khu_vuc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phuong_id')->constrained('phuong_xa')->onDelete('cascade');
            $table->string('ten', 255);
            $table->double('vi_do')->nullable();
            $table->double('kinh_do')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khu_vuc');
    }
};
