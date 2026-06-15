<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the organizer dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if (! $user->isOrganizer()) {
            abort(403, 'Unauthorized action.');
        }

        $eventIds = $user->events()->pluck('id')->toArray();
        $stats = $user->organizerStats();

        $totalScansToday = Activity::whereIn('event_id', $eventIds)
            ->where('activity_type', 'scan_checkpoint')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $totalPointPool = $user->events()->sum('total_point_pool');
        $totalDistributedPoints = \App\Models\CheckpointScan::whereIn('event_id', $eventIds)->sum('total_point');
        $totalRemainingPointPool = $totalPointPool - $totalDistributedPoints;

        $events = $user->events()
            ->withCount('participants')
            ->withCount('checkpoints')
            ->orderBy('start_date')
            ->get();

        $activeEvent = $user->events()
            ->where('is_active', true)
            ->whereDate('start_date', '<=', now()->toDateString())
            ->first();

        if (! $activeEvent) {
            $activeEvent = $user->events()->where('is_active', true)->first();
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

        $recentActivities = Activity::whereIn('event_id', $eventIds)
            ->with(['user', 'event'])
            ->latest()
            ->limit(10)
            ->get();

        return view('organizer.dashboard.index', [
            'user' => $user,
            'totalEvents' => $stats['total_events'],
            'activeEvents' => $stats['active_events'],
            'totalParticipants' => $stats['total_participants'],
            'totalScansToday' => $totalScansToday,
            'events' => $events,
            'activePerformance' => $activePerformance,
            'recentActivities' => $recentActivities,
            'totalPointPool' => $totalPointPool,
            'totalRemainingPointPool' => $totalRemainingPointPool,
            'totalDistributedPoints' => $totalDistributedPoints,
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
