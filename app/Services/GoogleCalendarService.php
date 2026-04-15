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
            return $event->htmlLink;
        } catch (\Exception $e) {
            Log::error('Google Calendar Event Creation Failed: ' . $e->getMessage());
            return null;
        }
    }
}
