<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetChatData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus seluruh data pesan, percakapan, dan pengingat untuk mengulang testing.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('Apakah Anda yakin ingin menghapus SELURUH data chat, grup, dan pengingat kalender?')) {
            return;
        }

        $this->info('Memulai pembersihan data...');

        try {
            // Untuk PostgreSQL (sesuai .env), kita gunakan TRUNCATE CASCADE agar relasi ikut terhapus
            DB::statement('TRUNCATE TABLE messages CASCADE');
            DB::statement('TRUNCATE TABLE conversation_users CASCADE');
            DB::statement('TRUNCATE TABLE conversations CASCADE');
            DB::statement('TRUNCATE TABLE lecturer_reminders CASCADE');
            DB::statement('TRUNCATE TABLE xp_logs CASCADE');
            
            // Opsional: Reset skor dosen kembali ke 0
            DB::table('lecturer_scores')->update(['total_xp' => 0, 'level' => 1]);

            $this->success('Data chat dan pengingat kalender berhasil dibersihkan!');
            $this->warn('Catatan: Event di Google Calendar asli tidak ikut terhapus secara otomatis karena databasenya sudah dibersihkan. Mohon hapus manual di Google Calendar jika diperlukan.');
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
