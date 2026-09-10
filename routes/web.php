<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdmissionYearController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\AdmissionYear;

Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('staff.dashboard');
    }

    return redirect()->route('login');

});

Route::get('/test', function () {
    $data = [
        'title' => 'Admission Year',
        'route' => 'admission-year.',
        'hideCreate' => false,
        'add_button_name' => 'Add Admission Year'
    ];
    $data['admissionYears'] = AdmissionYear::paginate(10);
    return view('admin.admissionYears.index', $data);
})->name('test');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated & Active Routes
Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/admission-year', [AdmissionYearController::class, 'index'])->name('admission-year.index');

    Route::middleware(EnsureRole::class . ':admin')->group(function () {
        Route::get('/admission-year/setup', [AdmissionYearController::class, 'showAdmissionYearSetup'])->name('admission-year.setup');
        Route::get('/admission-year/create', [AdmissionYearController::class, 'getAddmissionYearCreate'])->name('admission-year.create');
        Route::post('/admission-year/setup', [AdmissionYearController::class, 'storeAdmissionYear'])->name('admission-year.store');
        Route::post('/admission-year/{admissionYear}/activate', [AdmissionYearController::class, 'activateAdmissionYear'])->name('admission-year.activate');
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
    
