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
        Schema::create('muc_do_su_kien', function (Blueprint $table) {
            $table->id();
            $table->string('ma', 50)->unique()->comment('THAP / TB / CAO / NGHIEM_TRONG');
            $table->string('ten', 255)->comment('Tên mức độ');
            $table->text('mo_ta')->nullable()->comment('Mô tả chi tiết');
            $table->integer('uu_tien')->default(0)->comment('Độ ưu tiên (số càng cao càng nghiêm trọng)');
            $table->timestamps();
            
            // Indexes
            $table->index('ma');
            $table->index('uu_tien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('muc_do_su_kien');
    }
};
