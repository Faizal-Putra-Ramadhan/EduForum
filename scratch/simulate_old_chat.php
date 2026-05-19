<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;

// 1. Cari atau buat Mahasiswa dan Dosen untuk testing
$student = User::where('role', 'mahasiswa')->first();
$lecturer = User::where('role', 'dosen')->first();

if (!$student) {
    echo "Membuat dummy mahasiswa...\n";
    $student = User::create([
        'name' => 'Test Student',
        'email' => 'student@test.com',
        'password' => bcrypt('password'),
        'role' => 'mahasiswa'
    ]);
}

if (!$lecturer) {
    echo "Membuat dummy dosen...\n";
    $lecturer = User::create([
        'name' => 'Test Lecturer',
        'email' => 'lecturer@test.com',
        'password' => bcrypt('password'),
        'role' => 'dosen'
    ]);
}

echo "Menggunakan Mahasiswa: {$student->name}\n";
echo "Menggunakan Dosen: {$lecturer->name}\n";

// 2. Cari percakapan privat antara mereka
$conversation = Conversation::where('type', 'private')
    ->whereHas('conversationUsers', function($q) use ($student) {
        $q->where('user_id', $student->id);
    })
    ->whereHas('conversationUsers', function($q) use ($lecturer) {
        $q->where('user_id', $lecturer->id);
    })
    ->first();

if (!$conversation) {
    echo "Membuat percakapan baru...\n";
    $conversation = Conversation::create(['type' => 'private', 'last_message_at' => Carbon::now()->subDays(2)]);
    $conversation->conversationUsers()->create(['user_id' => $student->id, 'role' => 'member']);
    $conversation->conversationUsers()->create(['user_id' => $lecturer->id, 'role' => 'member']);
}

// 3. Tambahkan pesan dari mahasiswa yang "basi" (dikirim 2 hari lalu)
$oldDate = Carbon::now()->subDays(2);
$message = Message::create([
    'conversation_id' => $conversation->id,
    'sender_id' => $student->id,
    'content' => 'Halo Pak, ini pesan uji coba untuk pengetesan Google Calendar yang dikirim 2 hari lalu.',
    'created_at' => $oldDate,
    'updated_at' => $oldDate
]);

// 4. Update last_message_at di Conversation
$conversation->update(['last_message_at' => $oldDate]);

echo "Berhasil! Chat uji coba telah dibuat (Chat ID: {$conversation->id}).\n";
echo "Sekarang silakan jalankan: php artisan app:sync-calendar-reminders\n";
