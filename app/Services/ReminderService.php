<?php

namespace App\Services;

use App\Models\User;
use App\Models\LecturerReminder;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReminderService
{
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * Add or update a reminder for a lecturer.
     */
    public function upsertReminder(User $lecturer, User $student, $groupName = null)
    {
        $reminder = LecturerReminder::firstOrCreate(
            ['lecturer_id' => $lecturer->id],
            ['unreplied_students' => [], 'group_sources' => []]
        );

        $students = $reminder->unreplied_students ?? [];
        $groups = $reminder->group_sources ?? [];

        // Add student if not already in list
        if (!in_array($student->id, $students)) {
            $students[] = $student->id;
        }

        // Add group name if provided and not in list
        if ($groupName && !in_array($groupName, $groups)) {
            $groups[] = $groupName;
        }

        $reminder->unreplied_students = $students;
        $reminder->group_sources = $groups;

        $count = count($students);
        $summary = "Ada {$count} mahasiswa mengirim pesan";
        
        $description = "Mahasiswa yang menunggu balasan:\n";
        $studentNames = User::whereIn('id', $students)->pluck('name')->toArray();
        foreach ($studentNames as $name) {
            $description .= "- {$name}\n";
        }

        if (!empty($groups)) {
            $description .= "\nSumber Grup:\n- " . implode("\n- ", $groups);
        }

        $description .= "\n\nBalas segera di: " . config('app.url') . "/forum";

        if (!$reminder->event_id) {
            // Create New Event
            $startTime = Carbon::now()->addMinutes(5); // Start soon
            $endTime = (clone $startTime)->addMinutes(30);
            
            $eventId = $this->googleService->createEvent($lecturer, $summary, $description, $startTime, $endTime);
            if ($eventId) {
                $reminder->event_id = $eventId;
            }
        } else {
            // Update Existing Event
            $this->googleService->updateEvent($lecturer, $reminder->event_id, $summary, $description);
        }

        $reminder->save();
        return $reminder;
    }

    /**
     * Remove a student from a lecturer's reminder list.
     */
    public function removeReminder(User $lecturer, User $student)
    {
        $reminder = LecturerReminder::where('lecturer_id', $lecturer->id)->first();
        if (!$reminder) return;

        $students = $reminder->unreplied_students ?? [];
        
        // Remove this specific student from the list
        $students = array_filter($students, function($id) use ($student) {
            return (string)$id !== (string)$student->id;
        });

        if (empty($students)) {
            // All students replied to, delete event
            if ($reminder->event_id) {
                $this->googleService->deleteEvent($lecturer, $reminder->event_id);
            }
            $reminder->delete();
            Log::info("Lecturer {$lecturer->id} reminder cleared.");
        } else {
            // Update the event with remaining students
            $reminder->unreplied_students = array_values($students);
            
            $count = count($students);
            $summary = "Ada {$count} mahasiswa mengirim pesan";
            
            $description = "Mahasiswa yang masih menunggu balasan:\n";
            $studentNames = User::whereIn('id', $students)->pluck('name')->toArray();
            foreach ($studentNames as $name) {
                $description .= "- {$name}\n";
            }
            
            $description .= "\nBalas segera di: " . config('app.url') . "/forum";

            $this->googleService->updateEvent($lecturer, $reminder->event_id, $summary, $description);
            $reminder->save();
        }
    }
}
