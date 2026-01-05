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
            // Remove redundant location fields
            // thanh_pho_id: can be derived from phuong_xa_id -> phuong_xa.thanh_pho_id
            // duong_id: too detailed for user info, phuong_xa_id is sufficient
            // phuong_id: was added by mistake, should be phuong_xa_id
            
            // Drop foreign key first if phuong_id exists
            if (Schema::hasColumn('nguoi_dung', 'phuong_id')) {
                $table->dropForeign(['phuong_id']);
                $table->dropColumn('phuong_id');
            }
            
            // Check and drop other columns if they exist
            if (Schema::hasColumn('nguoi_dung', 'thanh_pho_id')) {
                $table->dropColumn('thanh_pho_id');
            }
            if (Schema::hasColumn('nguoi_dung', 'duong_id')) {
                $table->dropColumn('duong_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            // Restore columns if migration is rolled back
            $table->unsignedBigInteger('thanh_pho_id')->nullable()->after('email');
            $table->unsignedBigInteger('duong_id')->nullable()->after('khu_vuc_id');
        });
    }
};
