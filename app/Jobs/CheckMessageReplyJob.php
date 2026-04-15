<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckMessageReplyJob implements ShouldQueue
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
     */
    public function handle(GoogleCalendarService $googleService): void
    {
        // Check if the message has been replied to
        $hasReply = Message::where('conversation_id', $this->message->conversation_id)
            ->where('sender_id', '!=', $this->message->sender_id)
            ->where('created_at', '>', $this->message->created_at)
            ->exists();

        if ($hasReply) {
            Log::info("Message ID {$this->message->id} has been replied to. Skipping reminder.");
            return;
        }

        // Get the recipient (User B)
        $conversation = Conversation::with('conversationUsers.user')->find($this->message->conversation_id);
        if (!$conversation) return;

        $recipientMapping = $conversation->conversationUsers->filter(function ($cu) {
            return (string)$cu->user_id !== (string)$this->message->sender_id;
        })->first();

        $recipient = $recipientMapping ? $recipientMapping->user : null;

        if (!$recipient) return;

        Log::info("Message ID {$this->message->id} not replied to within 1 minute. Creating Google Calendar reminder for User {$recipient->id}");

        // Create Google Calendar Event for tomorrow at 08:00 AM
        if ($recipient->google_token) {
            $tomorrow = Carbon::tomorrow()->setHour(8)->setMinute(0);
            $endTime = (clone $tomorrow)->addMinutes(30);

            $summary = "Balas Pesan dari " . $this->message->sender->name;
            $description = "Pesan masuk: \"" . $this->message->content . "\"\nSilakan balas di EduForum.";

            $eventLink = $googleService->createEvent($recipient, $summary, $description, $tomorrow, $endTime);

            if ($eventLink) {
                Log::info("Google Calendar event created: " . $eventLink);
            }
        } else {
            Log::info("Recipient {$recipient->id} has no Google Calendar connected. Skipping event creation.");
        }

        // Schedule WhatsApp reminder for tomorrow at 08:00 AM
        // We delay it until tomorrow morning
        $delaySeconds = Carbon::now()->diffInSeconds(Carbon::tomorrow()->setHour(8)->setMinute(0));
        if ($delaySeconds < 0) $delaySeconds = 0; // Already past 8am today, but tomorrow 8am is in the future

        SendWhatsAppReminderJob::dispatch($this->message)
            ->delay(now()->addSeconds($delaySeconds));
    }
}
