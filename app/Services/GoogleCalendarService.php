<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;
    protected $service;
    protected $calendarId;

    public function __construct()
    {
        $this->calendarId = env('GOOGLE_CALENDAR_ID');
        $jsonPath = base_path(env('GOOGLE_SERVICE_ACCOUNT_JSON_PATH', 'storage/app/google/service-account.json'));

        if (!file_exists($jsonPath)) {
            Log::warning("Google Service Account JSON file not found at: {$jsonPath}. Komponen Calendar akan melewati eksekusi.");
            return;
        }

        try {
            $this->client = new Client();
            $this->client->setAuthConfig($jsonPath);
            $this->client->addScope(Calendar::CALENDAR);
            $this->client->setAccessType('offline');

            $this->service = new Calendar($this->client);
        } catch (\Exception $e) {
            Log::error("Gagal inisialisasi Google Calendar Client: " . $e->getMessage());
        }
    }

    /**
     * Membuat atau memperbarui event pengingat di Google Calendar.
     * 
     * @param string $id Identifier unik untuk mencocokkan event (misal: ID Percakapan)
     * @param string $title Judul event
     * @param string $description Deskripsi event
     * @param \DateTime $startTime Waktu mulai
     * @param \DateTime $endTime Waktu selesai
     * @return Event|null
     */
    public function createOrUpdateReminder($id, $title, $description, $startTime, $endTime)
    {
        if (!$this->service) {
            return null;
        }

        $eventData = [
            'summary' => $title,
            'description' => $description,
            'start' => [
                'dateTime' => $startTime->format(\DateTime::RFC3339),
                'timeZone' => config('app.timezone', 'UTC'),
            ],
            'end' => [
                'dateTime' => $endTime->format(\DateTime::RFC3339),
                'timeZone' => config('app.timezone', 'UTC'),
            ],
            'extendedProperties' => [
                'private' => [
                    'eduforum_id' => (string)$id,
                ],
            ],
        ];

        try {
            // Mencari event yang sudah ada dengan ID unik eduforum_id
            $events = $this->service->events->listEvents($this->calendarId, [
                'privateExtendedProperty' => "eduforum_id=$id"
            ]);

            if (count($events->getItems()) > 0) {
                // Update event yang sudah ada
                $existingEvent = $events->getItems()[0];
                $event = new Event($eventData);
                return $this->service->events->update($this->calendarId, $existingEvent->getId(), $event);
            } else {
                // Buat event baru
                $event = new Event($eventData);
                return $this->service->events->insert($this->calendarId, $event);
            }
        } catch (\Exception $e) {
            Log::error("Kesalahan Google Calendar Service: " . $e->getMessage());
            return null;
        }
    }
}
