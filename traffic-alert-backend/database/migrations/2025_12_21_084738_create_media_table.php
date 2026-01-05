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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bai_dang_id')->constrained('bai_dang')->onDelete('cascade');
            $table->string('loai_media', 50); // image, video
            $table->string('duong_dan', 500);
            $table->bigInteger('kich_thuoc_byte')->nullable();
            $table->string('dinh_dang', 100)->nullable();
            $table->integer('thoi_luong')->nullable(); // for videos in seconds
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
