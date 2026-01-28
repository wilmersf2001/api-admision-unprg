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
        Schema::create('tb_distrito', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->string('ubigeo', 6);
            $table->foreignId('provincia_id')->constrained('tb_provincia')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_distrito');
    }
};
