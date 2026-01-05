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
        Schema::create('phuong_xa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thanh_pho_id')->constrained('thanh_pho')->onDelete('cascade');
            $table->string('ten', 255);
            $table->string('ma', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phuong_xa');
    }
};
