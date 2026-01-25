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
        Schema::create('tb_colegio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->string('centro_poblado', 60);
            $table->integer('tipo');
            $table->string('ubigeo', 6);
            $table->foreignId('distrito_id')->constrained('tb_distrito')->onDelete('cascade');
            $table->boolean('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_colegio');
    }
};
