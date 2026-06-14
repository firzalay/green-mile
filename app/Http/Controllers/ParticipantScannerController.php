<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Checkpoint;
use App\Models\CheckpointScan;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ParticipantScannerController extends Controller
{
    /**
     * Show the QR Scanner page.
     */
    public function index(Request $request): View
    {
        return view('participant.scanner.index');
    }

    /**
     * Process the scanned QR Code token.
     */
    public function scan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_token' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $checkpoint = Checkpoint::with('event')->where('qr_token', $request->qr_token)->first();

        // 1. QR Token must exist
        if (! $checkpoint) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak valid.',
            ], 422);
        }

        // 2. Checkpoint must be active
        if (strtolower($checkpoint->status) !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Checkpoint tidak aktif.',
            ], 422);
        }

        // 3. Event must be ongoing
        $event = $checkpoint->event;
        if (strtolower($event->status) !== 'ongoing') {
            return response()->json([
                'status' => 'error',
                'message' => 'Event tidak sedang berlangsung.',
            ], 422);
        }

        // 4. Participant must be registered / joined in the event
        $user = $request->user();
        $participant = EventParticipant::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $participant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum terdaftar pada event ini.',
            ], 422);
        }

        // 5. Prevent duplicate scan
        $alreadyScanned = CheckpointScan::where('user_id', $user->id)
            ->where('checkpoint_id', $checkpoint->id)
            ->exists();

        if ($alreadyScanned) {
            return response()->json([
                'status' => 'error',
                'message' => 'Checkpoint ini sudah pernah Anda scan.',
            ], 422);
        }

        // Process scan transaction
        try {
            DB::transaction(function () use ($user, $event, $checkpoint, $participant) {
                // Lock the event row for update
                $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->first();

                if ($lockedEvent->remaining_point_pool < $checkpoint->points) {
                    throw new \Exception('Poin event telah habis.');
                }

                // Save scan history
                CheckpointScan::create([
                    'user_id' => $user->id,
                    'event_id' => $lockedEvent->id,
                    'checkpoint_id' => $checkpoint->id,
                    'points_awarded' => $checkpoint->points,
                    'scanned_at' => now(),
                ]);

                // Update participant progress
                $participant->increment('completed_checkpoints');
                $participant->increment('current_event_points', $checkpoint->points);
                $participant->increment('total_points', $checkpoint->points);

                // Record user Activity log
                Activity::create([
                    'user_id' => $user->id,
                    'event_id' => $lockedEvent->id,
                    'activity_type' => 'scan_checkpoint',
                    'description' => 'berhasil scan '.$checkpoint->name,
                    'points' => $checkpoint->points,
                ]);

                // Decrement remaining point pool
                $lockedEvent->decrement('remaining_point_pool', $checkpoint->points);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Checkpoint berhasil discan!',
            'checkpoint_name' => $checkpoint->name,
            'points_awarded' => $checkpoint->points,
            'total_points' => $participant->fresh()->current_event_points,
        ]);
    }
}
