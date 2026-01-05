<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add muc_do_id column
        Schema::table('bai_dang', function (Blueprint $table) {
            $table->foreignId('muc_do_id')->nullable()->after('loai_canh_bao')->constrained('muc_do_su_kien')->onDelete('cascade');
        });

        // 2. Migrate existing data if any
        $mucDoMap = DB::table('muc_do_su_kien')->pluck('id', 'ma');
        
        if ($mucDoMap->isNotEmpty()) {
            foreach ($mucDoMap as $ma => $id) {
                DB::table('bai_dang')
                    ->where('muc_do', $ma)
                    ->update(['muc_do_id' => $id]);
            }
        }

        // 3. Drop old muc_do column
        Schema::table('bai_dang', function (Blueprint $table) {
            $table->dropColumn('muc_do');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_dang', function (Blueprint $table) {
            $table->string('muc_do', 50)->after('loai_canh_bao');
        });

        // Optional: reverse migration of data
        $mucDoMap = DB::table('muc_do_su_kien')->pluck('ma', 'id');
        if ($mucDoMap->isNotEmpty()) {
            foreach ($mucDoMap as $id => $ma) {
                DB::table('bai_dang')
                    ->where('muc_do_id', $id)
                    ->update(['muc_do' => $ma]);
            }
        }

        Schema::table('bai_dang', function (Blueprint $table) {
            $table->dropForeign(['muc_do_id']);
            $table->dropColumn('muc_do_id');
        });
    }
};
