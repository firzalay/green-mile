<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizerEventController extends Controller
{
    /**
     * Display a listing of the organizer's events.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $events = $user->events()
            ->withCount(['participants', 'checkpoints'])
            ->orderByDesc('created_at')
            ->get();

        return view('organizer.events.index', compact('user', 'events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(Request $request): View
    {
        return view('organizer.events.create', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'banner' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'total_rewards' => ['nullable', 'string', 'max:255'],
            'max_points' => ['required', 'integer', 'min:1'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['organizer_id'] = $request->user()->id;
        $validated['status'] = 'draft';
        $validated['is_active'] = true;

        Event::create($validated);

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event berhasil dibuat sebagai draft.');
    }

    /**
     * Display the specified event.
     */
    public function show(Request $request, int $id): View
    {
        $user = $request->user();
        $event = Event::withCount(['participants', 'checkpoints'])
            ->findOrFail($id);

        if ($event->organizer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $totalPoints = $event->participants()->sum('current_event_points');
        $totalCheckpointsCompleted = $event->participants()->sum('completed_checkpoints');

        return view('organizer.events.show', compact('user', 'event', 'totalPoints', 'totalCheckpointsCompleted'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Request $request, int $id): View
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        if ($event->organizer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizer.events.edit', compact('user', 'event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        if ($event->organizer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'banner' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'total_rewards' => ['nullable', 'string', 'max:255'],
            'max_points' => ['required', 'integer', 'min:1'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:draft,published,ongoing,finished'],
        ]);

        $event->update($validated);

        return redirect()->route('organizer.events.show', $event->id)
            ->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        if ($event->organizer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $event->delete();

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    /**
     * Regenerate the join code for the specified event.
     */
    public function regenerateCode(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        if ($event->organizer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $event->join_code = Event::generateUniqueJoinCode();
        $event->save();

        return redirect()->route('organizer.events.show', $event->id)
            ->with('success', 'Kode akses event berhasil diperbarui.');
    }
}
