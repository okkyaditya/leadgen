<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\UplineChangeRequestController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Documentation
    Route::get('/documentation', function () {
        return Inertia::render('Documentation/Index');
    })->name('documentation.index');

    // Cabang
    Route::resource('cabang', CabangController::class)->except(['create', 'show', 'edit']);

    // Users
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    // Mitra
    Route::get('mitra/export', [MitraController::class, 'export'])->name('mitra.export');
    Route::resource('mitra', MitraController::class)->except(['create', 'show', 'edit']);

    // Leads (Export must come before resource to prevent id collision)
    Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::resource('leads', LeadController::class)->except(['create', 'show', 'edit']);

    // Upline Change Requests
    Route::resource('upline-requests', UplineChangeRequestController::class)->except(['create', 'show', 'edit']);

    // Audit Logs (Read-only)
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
