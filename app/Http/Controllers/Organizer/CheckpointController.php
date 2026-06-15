<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkpoint\StoreCheckpointRequest;
use App\Http\Requests\Checkpoint\UpdateCheckpointRequest;
use App\Models\Checkpoint;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckpointController extends Controller
{
    /**
     * Display a listing of the checkpoints for a specific event.
     */
    public function index(Request $request, Event $event): View
    {
        if ($event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $checkpoints = $event->checkpoints()
            ->orderBy('sequence')
            ->get();

        return view('organizer.checkpoints.index', compact('event', 'checkpoints'));
    }

    /**
     * Show the form for creating a new checkpoint.
     */
    public function create(Request $request, Event $event): View
    {
        if ($event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $nextSequence = $event->checkpoints()->max('sequence') + 1;

        return view('organizer.checkpoints.create', compact('event', 'nextSequence'));
    }

    /**
     * Store a newly created checkpoint in storage.
     */
    public function store(StoreCheckpointRequest $request, Event $event): RedirectResponse
    {
        if ($event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $event, $request) {
            $isCustom = $request->boolean('is_custom_point');
            $checkpoint = Checkpoint::create([
                'event_id' => $event->id,
                'name' => $validated['name'],
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'sequence' => $validated['sequence'],
                'status' => $validated['status'],
                'is_custom_point' => $isCustom,
                'point' => $isCustom ? (int) ($request->input('points') ?? 0) : 0,
            ]);

            if ($request->has('bonus_tiers')) {
                foreach ($request->input('bonus_tiers') as $tier) {
                    $checkpoint->bonusTiers()->create([
                        'rank_start' => (int) $tier['rank_start'],
                        'rank_end' => isset($tier['rank_end']) && $tier['rank_end'] !== '' ? (int) $tier['rank_end'] : null,
                        'bonus_percentage' => (float) $tier['bonus_percentage'],
                    ]);
                }
            }
        });

        return redirect()->route('organizer.events.checkpoints.index', $event->id)
            ->with('success', 'Checkpoint berhasil dibuat.');
    }

    /**
     * Display the specified checkpoint.
     */
    public function show(Request $request, Checkpoint $checkpoint): View
    {
        $checkpoint->loadMissing('event')->loadCount('scans');

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizer.checkpoints.show', compact('checkpoint'));
    }

    /**
     * Show the form for editing the specified checkpoint.
     */
    public function edit(Request $request, Checkpoint $checkpoint): View
    {
        $checkpoint->loadMissing('event');

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizer.checkpoints.edit', compact('checkpoint'));
    }

    /**
     * Update the specified checkpoint in storage.
     */
    public function update(UpdateCheckpointRequest $request, Checkpoint $checkpoint): RedirectResponse
    {
        $checkpoint->load('event');

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $checkpoint, $request) {
            $isCustom = $request->boolean('is_custom_point');
            $checkpoint->update([
                'name' => $validated['name'],
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'sequence' => $validated['sequence'],
                'status' => $validated['status'],
                'is_custom_point' => $isCustom,
                'point' => $isCustom ? (int) ($request->input('points') ?? 0) : 0,
            ]);

            $checkpoint->bonusTiers()->delete();
            if ($request->has('bonus_tiers')) {
                foreach ($request->input('bonus_tiers') as $tier) {
                    $checkpoint->bonusTiers()->create([
                        'rank_start' => (int) $tier['rank_start'],
                        'rank_end' => isset($tier['rank_end']) && $tier['rank_end'] !== '' ? (int) $tier['rank_end'] : null,
                        'bonus_percentage' => (float) $tier['bonus_percentage'],
                    ]);
                }
            }
        });

        // Trigger automatic redistribution for non-custom checkpoints
        $checkpoint->event->distributePointsAutomatically();

        return redirect()->route('organizer.checkpoints.show', $checkpoint->id)
            ->with('success', 'Checkpoint berhasil diperbarui.');
    }

    /**
     * Remove the specified checkpoint from storage.
     */
    public function destroy(Request $request, Checkpoint $checkpoint): RedirectResponse
    {
        $checkpoint->load('event');

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($checkpoint->scans()->exists()) {
            return back()->withErrors(['error' => 'Checkpoint tidak dapat dihapus karena sudah memiliki riwayat scan peserta.']);
        }

        $eventId = $checkpoint->event_id;
        $checkpoint->delete();

        return redirect()->route('organizer.events.checkpoints.index', $eventId)
            ->with('success', 'Checkpoint berhasil dihapus.');
    }
}
