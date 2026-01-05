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
            $table->string('trang_thai', 50)->default('active')->after('kich_hoat')->comment('active, inactive, pending...');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thiet_lap_canh_bao', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
