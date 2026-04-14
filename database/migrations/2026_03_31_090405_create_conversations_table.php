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
        Schema::connection('sqlite_messages')->dropIfExists('conversations');
        Schema::connection('sqlite_messages')->create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participant_1_id');
            $table->unsignedBigInteger('participant_2_id');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sqlite_messages')->dropIfExists('conversations');
    }
};
