<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizerDashboardController extends Controller
{
    /**
     * Display the organizer dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Ensure user is organizer
        if (! $user->isOrganizer()) {
            abort(403, 'Unauthorized action.');
        }

        $eventIds = $user->events()->pluck('id')->toArray();

        // Statistics Cards data
        $totalEvents = $user->events()->count();
        $activeEvents = $user->events()->where('is_active', true)->count();

        $totalParticipants = EventParticipant::whereIn('event_id', $eventIds)
            ->distinct('user_id')
            ->count('user_id');

        $totalScansToday = Activity::whereIn('event_id', $eventIds)
            ->where('activity_type', 'scan_checkpoint')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // Event Overview list
        $events = $user->events()
            ->withCount('participants')
            ->withCount('checkpoints')
            ->orderBy('event_date')
            ->get();

        // Active Event Performance (Ongoing event)
        $activeEvent = $user->events()
            ->where('is_active', true)
            ->whereDate('event_date', '<=', now()->toDateString())
            ->first();

        // Fallback to any active event if no ongoing today
        if (! $activeEvent) {
            $activeEvent = $user->events()
                ->where('is_active', true)
                ->first();
        }

        $activePerformance = null;
        if ($activeEvent) {
            $registeredCount = $activeEvent->participants()->count();

            $scansCount = Activity::where('event_id', $activeEvent->id)
                ->where('activity_type', 'scan_checkpoint')
                ->count();

            $pointsCount = EventParticipant::where('event_id', $activeEvent->id)
                ->sum('current_event_points');

            $totalCompleted = EventParticipant::where('event_id', $activeEvent->id)
                ->sum('completed_checkpoints');
            $totalPossible = $registeredCount * $activeEvent->total_checkpoints;
            $progressPercent = $totalPossible > 0 ? round(($totalCompleted / $totalPossible) * 100) : 0;

            $activePerformance = [
                'event' => $activeEvent,
                'registered_count' => $registeredCount,
                'scans_count' => $scansCount,
                'points_count' => $pointsCount,
                'progress_percent' => $progressPercent,
            ];
        }

        // Recent Activity feed
        $recentActivities = Activity::whereIn('event_id', $eventIds)
            ->with(['user', 'event'])
            ->latest()
            ->limit(10)
            ->get();

        return view('organizer.dashboard.index', [
            'user' => $user,
            'totalEvents' => $totalEvents,
            'activeEvents' => $activeEvents,
            'totalParticipants' => $totalParticipants,
            'totalScansToday' => $totalScansToday,
            'events' => $events,
            'activePerformance' => $activePerformance,
            'recentActivities' => $recentActivities,
        ]);
    }

    /**
     * Show placeholder page for features under development.
     */
    public function placeholder(Request $request, string $action = 'feature'): View
    {
        $user = $request->user();

        $labels = [
            'create-event' => 'Buat Event Baru',
            'checkpoints' => 'Kelola Checkpoint',
            'qr-generation' => 'Generate QR Code',
            'participants' => 'Lihat & Kelola Peserta',
        ];

        $title = $labels[$action] ?? 'Fitur Utama';

        return view('organizer.placeholder', [
            'user' => $user,
            'title' => $title,
        ]);
    }
}
