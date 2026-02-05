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
        Schema::table('tb_banco', function (Blueprint $table) {
            $table->foreignId('postulant_id')
                ->nullable()
                ->after('archivo_txt_id')
                ->constrained('tb_postulante')
                ->nullOnDelete();
            $table->timestamp('used_at')->nullable()->after('postulant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_banco', function (Blueprint $table) {
            $table->dropForeign(['postulant_id']);
            $table->dropColumn(['postulant_id', 'used_at']);
        });
    }
};
