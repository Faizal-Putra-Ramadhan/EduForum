<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;

class SyncCalendarReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-calendar-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi chat mahasiswa yang belum dibalas dosen (1 hari) ke Google Calendar';

    protected $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        parent::__construct();
        $this->calendarService = $calendarService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai sinkronisasi pengingat Google Calendar...');

        // Ambang batas: 24 jam yang lalu
        $threshold = Carbon::now()->subDay();

        // Ambil percakapan privat yang pesan terakhirnya sudah lebih dari 24 jam
        $conversations = Conversation::where('type', 'private')
            ->where('last_message_at', '<=', $threshold)
            ->with(['messages' => function($q) {
                $q->latest()->limit(1);
            }, 'conversationUsers.user'])
            ->get();

        if ($conversations->isEmpty()) {
            $this->info('Tidak ada chat yang memenuhi kriteria (belum dibalas > 24 jam).');
            return;
        }

        $count = 0;
        foreach ($conversations as $conversation) {
            $lastMessage = $conversation->messages->first();

            if (!$lastMessage) {
                continue;
            }

            // Memuat sender jika belum ada (eager loading di atas tidak memuat sender di dalam messages)
            $sender = $lastMessage->sender;
            
            // Pastikan pengirim pesan terakhir adalah mahasiswa
            if ($sender && $sender->role === 'mahasiswa') {
                // Cari dosen dalam percakapan tersebut
                $lecturerMapping = $conversation->conversationUsers->filter(function($cu) {
                    return $cu->user && $cu->user->role === 'dosen';
                })->first();

                $lecturer = $lecturerMapping ? $lecturerMapping->user : null;
                $lecturerName = $lecturer ? $lecturer->name : 'Dosen';

                $title = "Pesan Belum Dibalas: {$sender->name}";
                $snippet = substr($lastMessage->content, 0, 100) . (strlen($lastMessage->content) > 100 ? '...' : '');
                $chatUrl = config('app.url') . "/forum/" . $conversation->id;
                
                $description = "Mahasiswa {$sender->name} telah mengirim pesan yang belum dibalas selama lebih dari 24 jam.\n\n" .
                               "Pesan: \"{$snippet}\"\n\n" .
                               "Klik untuk membalas: {$chatUrl}\n\n" .
                               "Dosen Terkait: {$lecturerName}";

                // Waktu pengingat (setengah jam dari sekarang untuk notifikasi kalender)
                $startTime = Carbon::now()->addMinutes(30);
                $endTime = Carbon::now()->addHour();

                $result = $this->calendarService->createOrUpdateReminder(
                    $conversation->id,
                    $title,
                    $description,
                    $startTime,
                    $endTime
                );

                if ($result) {
                    $this->line("- Sinkronisasi berhasil untuk chat: {$sender->name} -> {$lecturerName}");
                    $count++;
                } else {
                    $this->error("- Gagal sinkronisasi untuk chat ID: {$conversation->id} (Periksa konfigurasi Google API atau file JSON)");
                }
            }
        }

        $this->info("Sinkronisasi selesai. Total {$count} pengingat diproses.");
    }
}
