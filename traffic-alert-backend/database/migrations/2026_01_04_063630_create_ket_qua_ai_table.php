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
        Schema::create('ket_qua_ai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
            $table->string('nhan', 100)->comment('Label detected by AI');
            $table->double('do_tin_cay')->comment('Confidence score');
            $table->json('raw_json')->nullable()->comment('Full AI response');
            $table->boolean('da_xac_minh')->default(false)->comment('Verified by human');
            $table->foreignId('xac_minh_boi')->nullable()->constrained('nguoi_dung')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('media_id');
            $table->index('nhan');
            $table->index('da_xac_minh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ket_qua_ai');
    }
};
