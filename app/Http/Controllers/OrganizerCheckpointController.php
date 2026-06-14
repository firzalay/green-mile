<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkpoint\StoreCheckpointRequest;
use App\Http\Requests\Checkpoint\UpdateCheckpointRequest;
use App\Models\Checkpoint;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizerCheckpointController extends Controller
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

        // Get the next sequence number by default
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
        $validated['event_id'] = $event->id;

        Checkpoint::create($validated);

        return redirect()->route('organizer.events.checkpoints.index', $event->id)
            ->with('success', 'Checkpoint berhasil dibuat.');
    }

    /**
     * Display the specified checkpoint.
     */
    public function show(Request $request, int $id): View
    {
        $checkpoint = Checkpoint::with('event')->findOrFail($id);

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizer.checkpoints.show', compact('checkpoint'));
    }

    /**
     * Show the form for editing the specified checkpoint.
     */
    public function edit(Request $request, int $id): View
    {
        $checkpoint = Checkpoint::with('event')->findOrFail($id);

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizer.checkpoints.edit', compact('checkpoint'));
    }

    /**
     * Update the specified checkpoint in storage.
     */
    public function update(UpdateCheckpointRequest $request, int $id): RedirectResponse
    {
        $checkpoint = Checkpoint::findOrFail($id);

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $checkpoint->update($request->validated());

        return redirect()->route('organizer.checkpoints.show', $checkpoint->id)
            ->with('success', 'Checkpoint berhasil diperbarui.');
    }

    /**
     * Remove the specified checkpoint from storage.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $checkpoint = Checkpoint::findOrFail($id);

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $eventId = $checkpoint->event_id;
        $checkpoint->delete();

        return redirect()->route('organizer.events.checkpoints.index', $eventId)
            ->with('success', 'Checkpoint berhasil dihapus.');
    }
}
