<?php

namespace App\Http\Controllers\Participant;

use App\Actions\Checkpoint\ProcessCheckpointScanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Scan\ScanQrRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScannerController extends Controller
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
    public function scan(ScanQrRequest $request, ProcessCheckpointScanAction $action): JsonResponse
    {
        try {
            $result = $action->execute($request->user(), $request->qr_token);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Checkpoint berhasil discan!',
            'checkpoint_name' => $result['checkpoint_name'],
            'points_awarded' => $result['points_awarded'],
            'total_points' => $result['total_points'],
        ]);
    }
}
