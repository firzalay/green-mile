<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Linkxtr\QrCode\Facades\QrCode;

class QrController extends Controller
{
    /**
     * Show the QR Code detail and preview page.
     */
    public function show(Request $request, int $id): View
    {
        $checkpoint = Checkpoint::with('event')->findOrFail($id);

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $qrCode = null;
        if ($checkpoint->qr_token) {
            $qrCode = QrCode::format('svg')->size(300)->generate($checkpoint->qr_token);
        }

        return view('organizer.qr.show', compact('checkpoint', 'qrCode'));
    }

    /**
     * Generate a new QR Code token for the checkpoint.
     */
    public function generate(Request $request, int $id): RedirectResponse
    {
        $checkpoint = Checkpoint::with('event')->findOrFail($id);

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if (strtolower($checkpoint->status) !== 'active') {
            return redirect()->back()->with('error', 'QR Code hanya dapat dibuat untuk checkpoint dengan status Active.');
        }

        $checkpoint->update([
            'qr_token' => (string) Str::uuid(),
        ]);

        return redirect()->route('organizer.checkpoints.qr.show', $checkpoint->id)
            ->with('success', 'QR Code berhasil dibuat.');
    }

    /**
     * Regenerate the QR Code token, invalidating the old one.
     */
    public function regenerate(Request $request, int $id): RedirectResponse
    {
        $checkpoint = Checkpoint::with('event')->findOrFail($id);

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if (strtolower($checkpoint->status) !== 'active') {
            return redirect()->back()->with('error', 'QR Code hanya dapat dibuat untuk checkpoint dengan status Active.');
        }

        $checkpoint->update([
            'qr_token' => (string) Str::uuid(),
        ]);

        return redirect()->route('organizer.checkpoints.qr.show', $checkpoint->id)
            ->with('success', 'QR Code berhasil diperbarui. Token lama tidak valid lagi.');
    }

    /**
     * Download the QR Code image (falls back to SVG if PNG fails).
     */
    public function download(Request $request, int $id): mixed
    {
        $checkpoint = Checkpoint::with('event')->findOrFail($id);

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if (! $checkpoint->qr_token) {
            abort(404, 'QR Code belum dibuat.');
        }

        $baseFilename = Str::slug($checkpoint->event->name.'-'.$checkpoint->name);

        try {
            $content = QrCode::format('png')->size(400)->generate($checkpoint->qr_token);
            $contentType = 'image/png';
            $filename = $baseFilename.'.png';
        } catch (\Exception $e) {
            $content = QrCode::format('svg')->size(400)->generate($checkpoint->qr_token);
            $contentType = 'image/svg+xml';
            $filename = $baseFilename.'.svg';
        }

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => $contentType,
        ]);
    }

    /**
     * Show printable layout of the QR Code.
     */
    public function print(Request $request, int $id): View
    {
        $checkpoint = Checkpoint::with('event')->findOrFail($id);

        if ($checkpoint->event->organizer_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if (! $checkpoint->qr_token) {
            abort(404, 'QR Code belum dibuat.');
        }

        $qrCode = QrCode::format('svg')->size(400)->generate($checkpoint->qr_token);

        return view('organizer.qr.print', compact('checkpoint', 'qrCode'));
    }
}
