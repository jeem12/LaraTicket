<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;

Route::get('/force-logout', function () {
    Auth::guard('web')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
});

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Volt::route('/dashboard', 'dashboard')->name('admin.dashboard');
    // Route::get('/users', [App\Http\Controllers\AdminController::class, 'userManagement'])->name('admin.users.index');
    Volt::route('/users', 'user-management')->name('admin.users.index');
    Volt::route('/departments', 'department-management')->name('admin.departments.index');

    Volt::route('/open-tickets', 'open-tickets')->name('admin.open-tickets');
    Volt::route('/pending-tickets', 'pending-tickets')->name('admin.pending-tickets');
    Volt::route('/closed-tickets', 'closed-tickets')->name('admin.closed-tickets');


    Route::get('/system-reports', [App\Http\Controllers\AdminController::class, 'indexReports'])->name('admin.reports');
    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'showSettings'])->name('admin.settings');



    Volt::route('/tickets/{status}', 'tickets.index')->name('tickets.index');
});

// // Admin routes (Controller-less)
// Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    
//     // Using Volt::route for direct component rendering
//     Volt::route('/dashboard', 'admin.dashboard')->name('admin.dashboard');
//     Volt::route('/users', 'admin.user-management')->name('admin.users.index');
//     Volt::route('/departments', 'admin.department-management')->name('admin.departments.index');
//     Volt::route('/system-reports', 'admin.system-reports')->name('admin.reports.index');

//     // If you need specific report views/details, you can point them to components
//     Volt::route('/reports/{id}', 'admin.reports.show')->name('admin.reports.show');

//     Volt::route('/settings', 'admin.settings')->name('admin.settings');
// });

// Regular user routes
Route::middleware(['auth', 'role:user'])->group(function () {
    // Settings (Already using Volt::route, which is correct)
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Dashboard & Tickets (Convert these to Volt routes)
<<<<<<< HEAD
    Volt::route('/Dashboard', 'user.dashboard')->name('user.dashboard');
=======
    Volt::route('/Dashboard', 'user-dashboard')->name('user.dashboard');
    Volt::route('/new-ticket', 'new-tickets')->name('user.new-ticket');
    Volt::route('/opened-ticket', 'opened-tickets')->name('user.opened-ticket');
    Volt::route('/pending-ticket', 'pending-tickets')->name('user.pending-tickets');
    Volt::route('/closed-ticket', 'closed-tickets')->name('user.closed-tickets');
>>>>>>> a7375cc (July 21, 2026 7:20)

    Route::prefix('userDashboard/tickets')->group(function () {
        Volt::route('/', 'user.tickets.index')->name('user.tickets.index');
        Volt::route('/create', 'user.tickets.create')->name('user.tickets.create');
        Volt::route('/{id}', 'user.tickets.show')->name('user.tickets.show');
    });
});

require __DIR__.'/auth.php';