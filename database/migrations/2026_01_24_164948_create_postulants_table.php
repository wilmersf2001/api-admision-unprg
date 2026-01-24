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
            $table->unsignedBigInteger('sexo_id');
            $table->unsignedBigInteger('distrito_nac_id'); //distrito donde nacio
            $table->unsignedBigInteger('distrito_res_id'); //distrito donde vive
            $table->unsignedBigInteger('tipo_direccion_id');
            $table->unsignedBigInteger('programa_academico_id');
            $table->unsignedBigInteger('colegio_id');
            $table->unsignedBigInteger('universidad_id')->nullable();
            $table->unsignedBigInteger('modalidad_id');
            $table->unsignedBigInteger('sede_id');
            $table->unsignedBigInteger('pais_id')->default(134);
            $table->unsignedBigInteger('estado_postulante_id')->default(1);
            $table->integer('ingreso')->nullable();
            $table->foreign('sexo_id')->references('id')->on('tb_sexo');
            $table->foreign('distrito_nac_id')->references('id')->on('tb_distrito');
            $table->foreign('distrito_res_id')->references('id')->on('tb_distrito');
            $table->foreign('tipo_direccion_id')->references('id')->on('tb_tipo_direccion');
            $table->foreign('programa_academico_id')->references('id')->on('tb_programa_academico');
            $table->foreign('colegio_id')->references('id')->on('tb_colegio');
            $table->foreign('universidad_id')->references('id')->on('tb_universidad');
            $table->foreign('modalidad_id')->references('id')->on('tb_modalidad');
            $table->foreign('sede_id')->references('id')->on('tb_sede');
            $table->foreign('pais_id')->references('id')->on('tb_pais');
            $table->foreign('estado_postulante_id')->references('id')->on('tb_estado_postulante');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulants');
    }
};
