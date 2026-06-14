<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
    public function store(StoreEventRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['organizer_id'] = $request->user()->id;
        $validated['status'] = 'draft';
        $validated['is_active'] = true;
        $validated['remaining_point_pool'] = $validated['point_pool'];

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('events', 'public');
            $validated['banner'] = $path;
        } else {
            $validated['banner'] = 'https://images.unsplash.com/photo-1502224562085-639556652f33?auto=format&fit=crop&q=80&w=800';
        }

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
        $event = Event::with(['rewards' => function ($query) {
            $query->orderBy('name')->limit(4);
        }])->withCount(['participants', 'checkpoints', 'rewards'])
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
    public function update(UpdateEventRequest $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        if ($event->organizer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        $distributed = $event->point_pool - $event->remaining_point_pool;
        if ($validated['point_pool'] < $distributed) {
            return back()->withErrors(['point_pool' => 'Total Point Pool tidak boleh kurang dari poin yang sudah dibagikan ('.number_format($distributed).' poin).'])->withInput();
        }

        $validated['remaining_point_pool'] = $validated['point_pool'] - $distributed;

        if ($request->hasFile('banner')) {
            $rawBanner = $event->getRawOriginal('banner');
            if ($rawBanner && ! filter_var($rawBanner, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($rawBanner);
            }
            $path = $request->file('banner')->store('events', 'public');
            $validated['banner'] = $path;
        } else {
            unset($validated['banner']);
        }

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

        $rawBanner = $event->getRawOriginal('banner');
        if ($rawBanner && ! filter_var($rawBanner, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($rawBanner);
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
