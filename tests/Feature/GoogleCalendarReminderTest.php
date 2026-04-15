<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationUser;
use App\Models\Message;
use App\Jobs\CheckMessageReplyJob;
use App\Jobs\SendWhatsAppReminderJob;
use App\Services\GoogleCalendarService;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Bus;
use Mockery;

class GoogleCalendarReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure the second connection is migrated for tests
        $this->artisan('migrate', [
            '--database' => 'sqlite_messages',
            '--path' => 'database/migrations',
        ]);
    }

    public function test_message_sent_dispatches_check_reply_job()
    {
        Bus::fake();

        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $conversation = Conversation::create(['type' => 'private']);
        
        // Use create instead of factory for ConversationUser due to multiple DBs
        ConversationUser::create(['conversation_id' => $conversation->id, 'user_id' => $userA->id]);
        ConversationUser::create(['conversation_id' => $conversation->id, 'user_id' => $userB->id]);

        $this->actingAs($userA);

        $response = $this->post(route('message.store', $conversation->id), [
            'content' => 'Hello User B'
        ]);

        $response->assertStatus(302);
        
        Bus::assertDispatched(CheckMessageReplyJob::class);
    }

    public function test_check_reply_job_creates_calendar_event_if_no_reply()
    {
        Bus::fake([SendWhatsAppReminderJob::class]);
        \Carbon\Carbon::setTestNow(now()->startOfDay()->addHours(10)); // 10:00 AM today

        $userA = User::factory()->create(['name' => 'Sender']);
        $userB = User::factory()->create([
            'name' => 'Recipient',
            'phone' => '08123456789',
            'google_token' => json_encode(['access_token' => 'test_token']),
        ]);

        $conversation = Conversation::create(['type' => 'private']);
        ConversationUser::create(['conversation_id' => $conversation->id, 'user_id' => $userA->id]);
        ConversationUser::create(['conversation_id' => $conversation->id, 'user_id' => $userB->id]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userA->id,
            'content' => 'Test message',
            'created_at' => now()->subMinutes(1)
        ]);

        // Mock Google Service
        $googleMock = Mockery::mock(GoogleCalendarService::class);
        $googleMock->shouldReceive('createEvent')
            ->once()
            ->andReturn('https://calendar.google.com/event?id=123');
        $this->app->instance(GoogleCalendarService::class, $googleMock);

        CheckMessageReplyJob::dispatchSync($message);

        Bus::assertDispatched(SendWhatsAppReminderJob::class);
        
        \Carbon\Carbon::setTestNow(); // Reset time
    }

    public function test_check_reply_job_skips_if_reply_exists()
    {
        Bus::fake([SendWhatsAppReminderJob::class]);
        \Carbon\Carbon::setTestNow(now()->startOfDay()->addHours(10));

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $conversation = Conversation::create(['type' => 'private']);
        ConversationUser::create(['conversation_id' => $conversation->id, 'user_id' => $userA->id]);
        ConversationUser::create(['conversation_id' => $conversation->id, 'user_id' => $userB->id]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userA->id,
            'content' => 'First message',
            'created_at' => now()->subMinutes(2)
        ]);

        // Create a reply
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userB->id,
            'content' => 'Reply message',
            'created_at' => now()->subMinutes(1)
        ]);

        $googleMock = Mockery::mock(GoogleCalendarService::class);
        $googleMock->shouldReceive('createEvent')->never();
        $this->app->instance(GoogleCalendarService::class, $googleMock);

        CheckMessageReplyJob::dispatchSync($message);

        Bus::assertNotDispatched(SendWhatsAppReminderJob::class);
        
        \Carbon\Carbon::setTestNow();
    }
}
