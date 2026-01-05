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
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->unsignedBigInteger('thanh_pho_id')->nullable()->after('email');
            $table->unsignedBigInteger('phuong_xa_id')->nullable()->after('thanh_pho_id');
            $table->unsignedBigInteger('duong_id')->nullable()->after('khu_vuc_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn(['thanh_pho_id', 'phuong_xa_id', 'duong_id']);
        });
    }
};
