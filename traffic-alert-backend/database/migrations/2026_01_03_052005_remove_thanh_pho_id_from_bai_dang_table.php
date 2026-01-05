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
            $table->dropForeign(['thanh_pho_id']);
            $table->dropColumn('thanh_pho_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_dang', function (Blueprint $table) {
            $table->unsignedBigInteger('thanh_pho_id')->nullable()->after('phuong_xa_id');
            $table->foreign('thanh_pho_id')->references('id')->on('thanh_pho')->onDelete('set null');
        });
    }
};
