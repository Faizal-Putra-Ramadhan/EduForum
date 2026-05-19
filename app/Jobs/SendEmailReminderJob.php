<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Conversation;
use App\Mail\NewMessageMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     * 
     * TAHAP 1: Kirim email ke dosen jika belum dibalas setelah delay.
     */
    public function handle(): void
    {
        // Cek apakah dosen sudah membalas
        $hasReply = Message::where('conversation_id', $this->message->conversation_id)
            ->where('sender_id', '!=', $this->message->sender_id)
            ->where('created_at', '>', $this->message->created_at)
            ->exists();

        if ($hasReply) {
            Log::info("[Eskalasi Tahap 1] Message ID {$this->message->id} sudah dibalas. Skip email.");
            return;
        }

        // Cari dosen (recipient)
        $conversation = Conversation::with('conversationUsers.user')->find($this->message->conversation_id);
        if (!$conversation) return;

        $recipientMapping = $conversation->conversationUsers->filter(function ($cu) {
            return (string)$cu->user_id !== (string)$this->message->sender_id;
        })->first();

        $recipient = $recipientMapping ? $recipientMapping->user : null;

        if (!$recipient) {
            Log::warning("[Eskalasi Tahap 1] Tidak ada recipient untuk message ID {$this->message->id}");
            return;
        }

        Log::info("[Eskalasi Tahap 1] Mengirim email ke dosen {$recipient->name} ({$recipient->email}) untuk message ID {$this->message->id}");

        try {
            $sender = $this->message->sender;
            $actionUrl = url('/forum/' . $conversation->id);
            $groupName = $conversation->type === 'group' ? $conversation->name : 'Private Consultation';

            Mail::to($recipient->email)->send(new NewMessageMail($sender, $this->message->content, $groupName, $actionUrl));

            Log::info("[Eskalasi Tahap 1] Email berhasil dikirim ke {$recipient->email}");
        } catch (\Exception $e) {
            Log::error("[Eskalasi Tahap 1] Gagal kirim email: " . $e->getMessage());
        }
    }
}
