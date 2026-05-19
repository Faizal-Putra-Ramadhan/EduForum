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
     * Track bahwa ada mahasiswa yang menunggu balasan dosen.
     * TIDAK langsung membuat Calendar event — itu dilakukan oleh SendCalendarReminderJob.
     */
    public function trackReminder(User $lecturer, User $student, $groupName = null)
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
        $reminder->save();

        Log::info("Reminder tracked: Dosen {$lecturer->name} | Mahasiswa {$student->name} | Total menunggu: " . count($students));

        return $reminder;
    }

    /**
     * Remove a student from a lecturer's reminder list.
        * Tidak melakukan penghapusan event Google Calendar otomatis.
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
            // All students replied to, remove reminder record only
            $reminder->delete();
            Log::info("Reminder untuk dosen {$lecturer->name} dihapus (semua sudah dibalas).");
        } else {
            // Update the event with remaining students
            $reminder->unreplied_students = array_values($students);
            
            $count = count($students);
            $summary = "📩 Ada {$count} mahasiswa menunggu balasan di EduForum";
            
            $description = "Mahasiswa yang masih menunggu balasan:\n";
            $studentNames = User::whereIn('id', $students)->pluck('name')->toArray();
            foreach ($studentNames as $name) {
                $description .= "- {$name}\n";
            }
            
            $description .= "\nBalas segera di: " . config('app.url') . "/forum";

            if ($reminder->event_id) {
                $this->googleService->updateEvent($lecturer, $reminder->event_id, $summary, $description);
            }
            $reminder->save();
            
            Log::info("Reminder updated: Dosen {$lecturer->name} | Sisa menunggu: {$count}");
        }
    }
}
