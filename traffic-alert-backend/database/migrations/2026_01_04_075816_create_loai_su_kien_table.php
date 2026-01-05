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
        Schema::create('loai_su_kien', function (Blueprint $table) {
            $table->id();
            $table->string('ma', 50)->unique(); // TAC_DUONG, NGAP, TAI_NAN...
            $table->string('ten', 255);
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loai_su_kien');
    }
};
