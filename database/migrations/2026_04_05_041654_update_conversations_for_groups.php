<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update conversations table
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('avatar')->nullable();
            $table->string('type')->default('private'); // private or group
            $table->unsignedBigInteger('creator_id')->nullable();
            
            // Allow null for old columns during transition if needed
            $table->unsignedBigInteger('participant_1_id')->nullable()->change();
            $table->unsignedBigInteger('participant_2_id')->nullable()->change();
        });

        // 2. Create conversation_users pivot table
        Schema::create('conversation_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id');
            $table->string('role')->default('member'); // admin or member
            $table->timestamps();
        });

        // 3. Data Migration: Move existing DM participants to pivot table
        $conversations = DB::table('conversations')->get();
        foreach ($conversations as $conv) {
            if ($conv->participant_1_id && $conv->participant_2_id) {
                DB::table('conversation_users')->insert([
                    ['conversation_id' => $conv->id, 'user_id' => $conv->participant_1_id, 'role' => 'member', 'created_at' => now(), 'updated_at' => now()],
                    ['conversation_id' => $conv->id, 'user_id' => $conv->participant_2_id, 'role' => 'member', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_users');
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['name', 'avatar', 'type', 'creator_id']);
        });
    }
};
