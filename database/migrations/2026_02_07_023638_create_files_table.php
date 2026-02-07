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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->integer('entity_id');
            $table->string('name');
            $table->string('original_name');
            $table->string('type');
            $table->string('path');
            $table->string('disk')->default('public'); // public o private
            $table->boolean('is_public')->default(true);
            $table->string('type_entitie');
            $table->string('mime_type');
            $table->string('extension');
            $table->unsignedBigInteger('size'); // en bytes
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
