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
        Schema::create('tb_postulante', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 60);
            $table->string('ap_paterno', 50);
            $table->string('ap_materno', 50);
            $table->date('fecha_nacimiento');
            $table->string('num_documento', 9);
            $table->string('tipo_documento', 3);
            $table->string('num_documento_apoderado', 11)->nullable();
            $table->string('nombres_apoderado', 60)->nullable();
            $table->string('ap_paterno_apoderado', 50)->nullable();
            $table->string('ap_materno_apoderado', 50)->nullable();
            $table->char('num_voucher', 7);
            $table->string('direccion', 150);
            $table->string('correo', 60);
            $table->string('telefono', 9);
            $table->string('telefono_ap', 9);
            $table->year('anno_egreso');
            $table->dateTime('fecha_inscripcion');
            $table->tinyInteger('num_veces_unprg');
            $table->tinyInteger('num_veces_otros');
            $table->string('codigo', 8);
            $table->integer('ingreso')->nullable();
            $table->foreignId('sexo_id')->constrained('tb_sexo')->onDelete('cascade');
            $table->foreignId('distrito_nac_id')->constrained('tb_distrito')->onDelete('cascade');
            $table->foreignId('distrito_res_id')->constrained('tb_distrito')->onDelete('cascade');
            $table->foreignId('tipo_direccion_id')->constrained('tb_tipo_direccion')->onDelete('cascade');
            $table->foreignId('programa_academico_id')->constrained('tb_programa_academico')->onDelete('cascade');
            $table->foreignId('colegio_id')->constrained('tb_colegio')->onDelete('cascade');
            $table->foreignId('universidad_id')->nullable()->constrained('tb_universidad')->onDelete('cascade');
            $table->foreignId('modalidad_id')->constrained('tb_modalidad')->onDelete('cascade');
            $table->foreignId('sede_id')->constrained('tb_sede')->onDelete('cascade');
            $table->foreignId('pais_id')->default(134)->constrained('tb_pais')->onDelete('cascade');
            $table->foreignId('estado_postulante_id')->default(1)->constrained('tb_estado_postulante')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_postulante');
    }
};
