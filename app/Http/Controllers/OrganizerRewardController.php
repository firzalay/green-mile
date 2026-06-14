<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Reward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizerRewardController extends Controller
{
    /**
     * Display a listing of the rewards for a specific event.
     */
    public function index(Request $request, Event $event): View
    {
        if ($event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $rewards = $event->rewards()
            ->orderBy('name')
            ->get();

        return view('organizer.rewards.index', compact('event', 'rewards'));
    }

    /**
     * Show the form for creating a new reward.
     */
    public function create(Request $request, Event $event): View
    {
        if ($event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizer.rewards.create', compact('event'));
    }

    /**
     * Store a newly created reward in storage.
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        if ($event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'required_points' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('rewards', 'public');
            $validated['image'] = '/storage/'.$path;
        } else {
            $validated['image'] = 'https://images.unsplash.com/photo-1545239351-ef35f43d514b?auto=format&fit=crop&q=80&w=600';
        }

        $validated['event_id'] = $event->id;
        $validated['total_redeemed'] = 0;
        $validated['is_active'] = true;

        Reward::create($validated);

        return redirect()->route('organizer.events.rewards.index', $event->id)
            ->with('success', 'Reward berhasil dibuat.');
    }

    /**
     * Display the specified reward.
     */
    public function show(Request $request, int $id): View
    {
        $reward = Reward::with('event')->findOrFail($id);

        if ($reward->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizer.rewards.show', compact('reward'));
    }

    /**
     * Show the form for editing the specified reward.
     */
    public function edit(Request $request, int $id): View
    {
        $reward = Reward::with('event')->findOrFail($id);

        if ($reward->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizer.rewards.edit', compact('reward'));
    }

    /**
     * Update the specified reward in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $reward = Reward::findOrFail($id);

        if ($reward->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'required_points' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('rewards', 'public');
            $validated['image'] = '/storage/'.$path;
        } else {
            unset($validated['image']);
        }

        $reward->update($validated);

        return redirect()->route('organizer.rewards.show', $reward->id)
            ->with('success', 'Reward berhasil diperbarui.');
    }

    /**
     * Remove the specified reward from storage.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $reward = Reward::findOrFail($id);

        if ($reward->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($reward->total_redeemed > 0) {
            return back()
                ->with('error', 'Reward tidak dapat dihapus karena sudah memiliki riwayat penukaran.')
                ->withErrors(['total_redeemed' => 'Reward tidak dapat dihapus karena sudah memiliki riwayat penukaran.']);
        }

        $eventId = $reward->event_id;
        $reward->delete();

        return redirect()->route('organizer.events.rewards.index', $eventId)
            ->with('success', 'Reward berhasil dihapus.');
    }
}
