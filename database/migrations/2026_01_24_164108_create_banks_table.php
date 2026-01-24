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
        Schema::create('tb_banco', function (Blueprint $table) {
            $table->id();
            $table->string('num_oficina', 4);
            $table->string('cod_concepto', 5);
            $table->integer('tipo_doc_pago');
            $table->string('num_documento', 15);
            $table->string('importe', 11);
            $table->date('fecha');
            $table->time('hora');
            $table->tinyInteger('estado');
            $table->string('num_doc_depo', 9);
            $table->string('tipo_doc_depo', 3);
            $table->string('observacion_depo', 200)->nullable();
            $table->unsignedBigInteger('archivo_txt_id');
            $table->foreign('archivo_txt_id')->references('id')->on('tb_archivo_txt');
            $table->unique(['num_oficina', 'num_documento', 'fecha', 'num_doc_depo']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_banco');
    }
};
