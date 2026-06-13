<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrganizerController extends Controller
{
    /**
     * Display a listing of organizers, statistics, and approval history.
     */
    public function index(Request $request): View
    {
        $totalOrganizers = User::where('role', 'organizer')->count();
        $pendingCount = User::where('role', 'organizer')->where('status', 'pending')->count();
        $approvedCount = User::where('role', 'organizer')->where('status', 'approved')->count();
        $rejectedCount = User::where('role', 'organizer')->where('status', 'rejected')->count();

        $pendingOrganizers = User::where('role', 'organizer')
            ->where('status', 'pending')
            ->with('organizerProfile')
            ->orderBy('created_at', 'desc')
            ->get();

        $historyOrganizers = User::where('role', 'organizer')
            ->whereIn('status', ['approved', 'rejected'])
            ->with(['organizerProfile', 'approver'])
            ->orderBy('approved_at', 'desc')
            ->get();

        return view('admin.organizers.index', compact(
            'totalOrganizers',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'pendingOrganizers',
            'historyOrganizers'
        ));
    }

    /**
     * Display detail information of a registered organizer.
     */
    public function show(int $id): View
    {
        $organizer = User::where('role', 'organizer')
            ->with(['organizerProfile', 'approver'])
            ->findOrFail($id);

        return view('admin.organizers.show', compact('organizer'));
    }

    /**
     * Approve the organizer registration request.
     */
    public function approve(int $id): RedirectResponse
    {
        $organizer = User::where('role', 'organizer')
            ->with('organizerProfile')
            ->findOrFail($id);

        $organizer->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $orgName = $organizer->organizerProfile->organization_name ?? $organizer->name;

        return redirect()->route('admin.organizers.index')
            ->with('success', "Organizer {$orgName} berhasil disetujui.");
    }

    /**
     * Reject the organizer registration request.
     */
    public function reject(int $id): RedirectResponse
    {
        $organizer = User::where('role', 'organizer')
            ->with('organizerProfile')
            ->findOrFail($id);

        $organizer->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $orgName = $organizer->organizerProfile->organization_name ?? $organizer->name;

        return redirect()->route('admin.organizers.index')
            ->with('success', "Pendaftaran organizer {$orgName} telah ditolak.");
    }
}
