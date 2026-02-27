<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_update_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulant_id')->constrained('tb_postulante')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason');
            $table->text('note')->nullable();
            $table->string('unique_code', 64)->unique();
            $table->boolean('code_used')->default(false);
            $table->dateTime('code_expires_at');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->dateTime('attended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_update_requests');
    }
};