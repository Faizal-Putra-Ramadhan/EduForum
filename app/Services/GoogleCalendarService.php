<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Oauth2;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Calendar::CALENDAR);
        $this->client->addScope(Calendar::CALENDAR_EVENTS);
        $this->client->addScope('https://www.googleapis.com/auth/userinfo.profile');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    /**
     * Get the Google Client instance.
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set access token for a user, refreshing if necessary.
     */
    public function setAccessTokenForUser(User $user)
    {
        if (!$user->google_token) {
            return false;
        }

        $this->client->setAccessToken($user->google_token);

        if ($this->client->isAccessTokenExpired()) {
            if ($user->google_refresh_token) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                
                if (isset($newToken['error'])) {
                    Log::error('Google Token Refresh Error: ' . json_encode($newToken));
                    return false;
                }

                $user->update([
                    'google_token' => json_encode($newToken),
                    'google_token_expires_at' => now()->addSeconds($newToken['expires_in']),
                ]);
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Create a calendar event.
     */
    public function createEvent(User $user, $summary, $description, $startTime, $endTime)
    {
        if (!$this->setAccessTokenForUser($user)) {
            Log::warning("User {$user->id} has no valid Google Token.");
            return null;
        }

        $service = new Calendar($this->client);

        $event = new Event([
            'summary' => $summary,
            'description' => $description,
            'start' => [
                'dateTime' => $startTime->format(\DateTime::RFC3339),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => $endTime->format(\DateTime::RFC3339),
                'timeZone' => config('app.timezone'),
            ],
        ]);

        $calendarId = 'primary';
        try {
            $event = $service->events->insert($calendarId, $event);
            return $event->id; // Return ID instead of link for backend tracking
        } catch (\Exception $e) {
            Log::error('Google Calendar Event Creation Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a multi-day all-day calendar event.
     * Cocok untuk pengingat yang berlangsung beberapa hari.
     */
    public function createMultiDayEvent(User $user, $summary, $description, $startDate, $endDate)
    {
        if (!$this->setAccessTokenForUser($user)) {
            Log::warning("User {$user->id} has no valid Google Token.");
            return null;
        }

        $service = new Calendar($this->client);

        // All-day event menggunakan format 'date' (bukan 'dateTime')
        $event = new Event([
            'summary' => $summary,
            'description' => $description,
            'start' => [
                'date' => $startDate->format('Y-m-d'),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'date' => $endDate->format('Y-m-d'),
                'timeZone' => config('app.timezone'),
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'popup', 'minutes' => 480],  // Reminder jam 08:00 pagi
                    ['method' => 'popup', 'minutes' => 0],    // Reminder saat event dimulai
                ],
            ],
        ]);

        $calendarId = 'primary';
        try {
            $event = $service->events->insert($calendarId, $event);
            Log::info("Multi-day calendar event created: {$event->id} ({$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')})");
            return $event->id;
        } catch (\Exception $e) {
            Log::error('Google Calendar Multi-Day Event Creation Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing calendar event.
     *
     * For all-day reminder events, optional start/end date can be provided
     * so the existing event is moved to a new date range.
     */
    public function updateEvent(User $user, $eventId, $summary, $description, $startDate = null, $endDate = null)
    {
        if (!$this->setAccessTokenForUser($user)) return null;

        $service = new Calendar($this->client);
        $calendarId = 'primary';

        try {
            $event = $service->events->get($calendarId, $eventId);
            $event->setSummary($summary);
            $event->setDescription($description);

            if ($startDate && $endDate) {
                $event->setStart(new \Google\Service\Calendar\EventDateTime([
                    'date' => $startDate->format('Y-m-d'),
                    'timeZone' => config('app.timezone'),
                ]));

                $event->setEnd(new \Google\Service\Calendar\EventDateTime([
                    'date' => $endDate->format('Y-m-d'),
                    'timeZone' => config('app.timezone'),
                ]));
            }

            $updatedEvent = $service->events->update($calendarId, $eventId, $event);
            return $updatedEvent->id;
        } catch (\Exception $e) {
            Log::error('Google Calendar Event Update Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a calendar event.
     */
    public function deleteEvent(User $user, $eventId)
    {
        if (!$this->setAccessTokenForUser($user)) return false;

        $service = new Calendar($this->client);
        $calendarId = 'primary';

        try {
            $service->events->delete($calendarId, $eventId);
            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar Event Deletion Failed: ' . $e->getMessage());
            return false;
        }
    }
}
