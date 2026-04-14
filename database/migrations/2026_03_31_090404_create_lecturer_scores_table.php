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
        Schema::create('lecturer_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('total_xp')->default(0);
            $table->integer('total_replies')->default(0);
            $table->float('avg_response_hours')->default(0);
            $table->enum('badge', ['Sangat Fast Respon', 'Fast Respon', 'Lumayan Fast Respon', 'Kurang Fast Respon', 'Tidak Fast Respon'])->default('Kurang Fast Respon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer_scores');
    }
};
