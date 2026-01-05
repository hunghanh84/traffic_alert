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
        Schema::table('duong', function (Blueprint $table) {
            $table->json('coordinates')->nullable()->after('loai_duong')->comment('Array of [lat, lng] points for the street polyline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duong', function (Blueprint $table) {
            $table->dropColumn('coordinates');
        });
    }
};
