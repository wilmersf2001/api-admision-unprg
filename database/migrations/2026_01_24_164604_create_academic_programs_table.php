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
        Schema::create('tb_programa_academico', function (Blueprint $table) {
            $table->id();
            $table->char('codigo', 2);
            $table->string('nombre', 60);
            $table->integer('estado');
            $table->foreignId('sede_id')->constrained('tb_sede')->onDelete('cascade');
            $table->foreignId('facultad_id')->constrained('tb_facultad')->onDelete('cascade');
            $table->foreignId('grupo_academico_id')->constrained('tb_grupo_academico')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_programs');
    }
};
