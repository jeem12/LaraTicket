<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::prefix('users')->group(function () {
        Route::get('/', [AdminController::class, 'userManagement'])->name('admin.users.index');
        Route::get('/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/{id}', [AdminController::class, 'showUser'])->name('admin.users.show');
        Route::get('/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    });

    Route::prefix('reports')->group(function () {
        Route::get('/', [AdminController::class, 'indexReports'])->name('admin.reports');
        Route::get('/{id}', [AdminController::class, 'showReport'])->name('admin.reports.show');
    });

    Route::get('admin/settings', [AdminController::class, 'showSettings'])->name('admin.settings');
});

// Regular user routes
Route::middleware(['auth', 'role:user'])->group(function () {
    // Settings
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // User Dashboard & Tickets
    Route::get('/userDashboard', [UserController::class, 'index'])->name('user.dashboard');

    Route::prefix('userDashboard/tickets')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.tickets.index');
        Route::get('/create', [UserController::class, 'createTicket'])->name('user.tickets.create');
        Route::post('/', [UserController::class, 'storeTicket'])->name('user.tickets.store');
        Route::get('/{id}', [UserController::class, 'showTicket'])->name('user.tickets.show');
    });
});

require __DIR__.'/auth.php';