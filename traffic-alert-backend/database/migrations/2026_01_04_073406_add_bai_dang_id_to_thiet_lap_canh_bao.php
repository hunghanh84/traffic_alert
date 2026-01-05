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
        Schema::table('thiet_lap_canh_bao', function (Blueprint $table) {
            $table->foreignId('bai_dang_id')->nullable()->after('id')->constrained('bai_dang')->onDelete('cascade');
            $table->string('loai_canh_bao', 50)->nullable()->after('duong_id');
            // Make nguoi_dung_id nullable for system-generated alerts
            $table->foreignId('nguoi_dung_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thiet_lap_canh_bao', function (Blueprint $table) {
            $table->dropForeign(['bai_dang_id']);
            $table->dropColumn(['bai_dang_id', 'loai_canh_bao']);
        });
    }
};
