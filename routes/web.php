<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerCheckpointController;
use App\Http\Controllers\OrganizerDashboardController;
use App\Http\Controllers\OrganizerEventController;
use App\Http\Controllers\OrganizerQrController;
use App\Http\Controllers\ParticipantEventController;
use App\Http\Controllers\ParticipantScannerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isOrganizer()
            ? redirect()->route('organizer.dashboard')
            : redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{id}/join', [ParticipantEventController::class, 'join'])->name('events.join');
});

Route::middleware(['auth', 'role:participant'])->group(function () {
    Route::get('/scanner-test', function () {
        return view('scanner-test');
    });
    Route::get('/scanner', [ParticipantScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/scan', [ParticipantScannerController::class, 'scan'])->name('scanner.scan');
});

Route::middleware(['auth', 'role:organizer'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [OrganizerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('events', OrganizerEventController::class);

    // Checkpoints resourceful routes
    Route::get('events/{event}/checkpoints', [OrganizerCheckpointController::class, 'index'])->name('events.checkpoints.index');
    Route::get('events/{event}/checkpoints/create', [OrganizerCheckpointController::class, 'create'])->name('events.checkpoints.create');
    Route::post('events/{event}/checkpoints', [OrganizerCheckpointController::class, 'store'])->name('events.checkpoints.store');
    Route::get('checkpoints/{id}', [OrganizerCheckpointController::class, 'show'])->name('checkpoints.show');
    Route::get('checkpoints/{id}/edit', [OrganizerCheckpointController::class, 'edit'])->name('checkpoints.edit');
    Route::put('checkpoints/{id}', [OrganizerCheckpointController::class, 'update'])->name('checkpoints.update');
    Route::delete('checkpoints/{id}', [OrganizerCheckpointController::class, 'destroy'])->name('checkpoints.destroy');

    // QR Code Management routes
    Route::post('checkpoints/{id}/generate-qr', [OrganizerQrController::class, 'generate'])->name('checkpoints.generate-qr');
    Route::post('checkpoints/{id}/regenerate-qr', [OrganizerQrController::class, 'regenerate'])->name('checkpoints.regenerate-qr');
    Route::get('checkpoints/{id}/download-qr', [OrganizerQrController::class, 'download'])->name('checkpoints.download-qr');
    Route::get('checkpoints/{id}/print-qr', [OrganizerQrController::class, 'print'])->name('checkpoints.print-qr');
    Route::get('checkpoints/{id}/qr', [OrganizerQrController::class, 'show'])->name('checkpoints.qr.show');

    Route::get('/placeholder/{action?}', [OrganizerDashboardController::class, 'placeholder'])->name('placeholder');
});

Route::get('/forgot-password', function () {
    return redirect()->route('login');
})->name('password.request');
