<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('staff.dashboard');
    }

    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated & Active Routes
Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(EnsureRole::class . ':admin')->group(function () {
        Route::get('/admission-year/setup', [AdminController::class, 'showAdmissionYearSetup'])->name('admission-year.setup');
        Route::get('/admission-year/create', [AdminController::class, 'getAddmissionYearCreate'])->name('admission-year.create');
        Route::post('/admission-year/setup', [AdminController::class, 'storeAdmissionYear'])->name('admission-year.store');
        Route::post('/admission-year/{admissionYear}/activate', [AdminController::class, 'activateAdmissionYear'])->name('admission-year.activate');
    });

    Route::middleware(EnsureRole::class . ':admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::post('/staff', [AdminController::class, 'storeStaff'])->name('staff.store');
        Route::post('/staff/{user}/toggle-status', [AdminController::class, 'toggleStaffStatus'])->name('staff.toggle-status');
    });

    Route::middleware(EnsureRole::class . ':staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'index'])->name('dashboard');
    });
});
    