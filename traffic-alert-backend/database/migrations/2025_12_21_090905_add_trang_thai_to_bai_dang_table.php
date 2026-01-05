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
        Schema::table('bai_dang', function (Blueprint $table) {
            $table->string('trang_thai', 50)->default('cho_duyet')->after('mo_ta');
            // cho_duyet, da_duyet, tu_choi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_dang', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
