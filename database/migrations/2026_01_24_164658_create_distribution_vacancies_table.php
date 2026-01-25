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
        Schema::create('tb_distribucion_vacantes', function (Blueprint $table) {
            $table->id();
            $table->integer('vacantes');
            $table->boolean('estado')->default(1);
            $table->foreignId('programa_academico_id')->constrained('tb_programa_academico')->onDelete('cascade');
            $table->foreignId('modalidad_id')->constrained('tb_modalidad')->onDelete('cascade');
            $table->foreignId('sede_id')->constrained('tb_sede')->onDelete('cascade');
            $table->unique(['programa_academico_id', 'modalidad_id', 'sede_id'], 'unique_distribucion_vacante');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_distribucion_vacantes');
    }
};
