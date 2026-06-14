<?php

use App\Http\Controllers\AdminOrganizerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerCheckpointController;
use App\Http\Controllers\OrganizerDashboardController;
use App\Http\Controllers\OrganizerEventController;
use App\Http\Controllers\OrganizerQrController;
use App\Http\Controllers\OrganizerRegistrationController;
use App\Http\Controllers\OrganizerRewardController;
use App\Http\Controllers\ParticipantEventController;
use App\Http\Controllers\ParticipantRegistrationController;
use App\Http\Controllers\ParticipantScannerController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\RewardHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.organizers.index');
        }

        return auth()->user()->isOrganizer()
            ? redirect()->route('organizer.dashboard')
            : redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/register', function () {
    return redirect()->route('register.select-role');
})->name('register');
Route::get('/register/select-role', [RegistrationController::class, 'selectRole'])->name('register.select-role');

Route::get('/register/participant', [ParticipantRegistrationController::class, 'create'])->name('register.participant');
Route::post('/register/participant', [ParticipantRegistrationController::class, 'store']);

Route::get('/register/organizer', [OrganizerRegistrationController::class, 'create'])->name('register.organizer');
Route::post('/register/organizer', [OrganizerRegistrationController::class, 'store']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/join', [ParticipantEventController::class, 'showJoinForm'])->name('events.join');
    Route::post('/events/join', [ParticipantEventController::class, 'joinWithCode'])->name('events.join.submit');
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
});

Route::middleware(['auth', 'role:participant'])->group(function () {
    Route::get('/scanner-test', function () {
        return view('scanner-test');
    });
    Route::get('/scanner', [ParticipantScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/scan', [ParticipantScannerController::class, 'scan'])->name('scanner.scan');

    // Reward System routes
    Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
    Route::get('/rewards/history', [RewardHistoryController::class, 'index'])->name('rewards.history');
    Route::get('/rewards/{id}', [RewardController::class, 'show'])->name('rewards.show');
    Route::post('/rewards/{id}/redeem', [RewardController::class, 'redeem'])->name('rewards.redeem');
});

Route::middleware(['auth', 'role:organizer', 'organizer.approved'])->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/dashboard', [OrganizerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('events', OrganizerEventController::class);
    Route::post('events/{id}/regenerate-code', [OrganizerEventController::class, 'regenerateCode'])->name('events.regenerate-code');

    // Checkpoints resourceful routes
    Route::get('events/{event}/checkpoints', [OrganizerCheckpointController::class, 'index'])->name('events.checkpoints.index');
    Route::get('events/{event}/checkpoints/create', [OrganizerCheckpointController::class, 'create'])->name('events.checkpoints.create');
    Route::post('events/{event}/checkpoints', [OrganizerCheckpointController::class, 'store'])->name('events.checkpoints.store');
    Route::get('checkpoints/{id}', [OrganizerCheckpointController::class, 'show'])->name('checkpoints.show');
    Route::get('checkpoints/{id}/edit', [OrganizerCheckpointController::class, 'edit'])->name('checkpoints.edit');
    Route::put('checkpoints/{id}', [OrganizerCheckpointController::class, 'update'])->name('checkpoints.update');
    Route::delete('checkpoints/{id}', [OrganizerCheckpointController::class, 'destroy'])->name('checkpoints.destroy');

    // Rewards resourceful routes
    Route::get('events/{event}/rewards', [OrganizerRewardController::class, 'index'])->name('events.rewards.index');
    Route::get('events/{event}/rewards/create', [OrganizerRewardController::class, 'create'])->name('events.rewards.create');
    Route::post('events/{event}/rewards', [OrganizerRewardController::class, 'store'])->name('events.rewards.store');
    Route::get('rewards/{id}', [OrganizerRewardController::class, 'show'])->name('rewards.show');
    Route::get('rewards/{id}/edit', [OrganizerRewardController::class, 'edit'])->name('rewards.edit');
    Route::put('rewards/{id}', [OrganizerRewardController::class, 'update'])->name('rewards.update');
    Route::delete('rewards/{id}', [OrganizerRewardController::class, 'destroy'])->name('rewards.destroy');

    // QR Code Management routes
    Route::post('checkpoints/{id}/generate-qr', [OrganizerQrController::class, 'generate'])->name('checkpoints.generate-qr');
    Route::post('checkpoints/{id}/regenerate-qr', [OrganizerQrController::class, 'regenerate'])->name('checkpoints.regenerate-qr');
    Route::get('checkpoints/{id}/download-qr', [OrganizerQrController::class, 'download'])->name('checkpoints.download-qr');
    Route::get('checkpoints/{id}/print-qr', [OrganizerQrController::class, 'print'])->name('checkpoints.print-qr');
    Route::get('checkpoints/{id}/qr', [OrganizerQrController::class, 'show'])->name('checkpoints.qr.show');

    Route::get('/placeholder/{action?}', [OrganizerDashboardController::class, 'placeholder'])->name('placeholder');
});

Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/organizers', [AdminOrganizerController::class, 'index'])->name('organizers.index');
    Route::get('/organizers/{id}', [AdminOrganizerController::class, 'show'])->name('organizers.show');
    Route::post('/organizers/{id}/approve', [AdminOrganizerController::class, 'approve'])->name('organizers.approve');
    Route::post('/organizers/{id}/reject', [AdminOrganizerController::class, 'reject'])->name('organizers.reject');
});

Route::get('/forgot-password', function () {
    return redirect()->route('login');
})->name('password.request');
