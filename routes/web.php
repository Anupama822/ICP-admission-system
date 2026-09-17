<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdmissionYearController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\InstituteController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffManagementController;
use App\Http\Controllers\StudentController;
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

Route::get('/test', function () {
    return view('test');
});
// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated & Active Routes
Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // The index also serves the DataTables ajax payload and the csv/excel/pdf/print exports.
    Route::get('/admission-year', [AdmissionYearController::class, 'index'])->name('admission-year.index');

    // Self-service password change, available to admin and staff alike.
    Route::get('/account/password', [AccountController::class, 'edit'])->name('account.password.edit');
    Route::put('/account/password', [AccountController::class, 'update'])->name('account.password.update');

    Route::middleware(EnsureRole::class.':admin')->group(function () {
        // Static segments first so they are not swallowed by /{admissionYear}.
        Route::get('/admission-year/setup', [AdmissionYearController::class, 'showAdmissionYearSetup'])->name('admission-year.setup');
        Route::post('/admission-year/setup', [AdmissionYearController::class, 'storeAdmissionYear'])->name('admission-year.setup.store');
        Route::get('/admission-year/create', [AdmissionYearController::class, 'create'])->name('admission-year.create');

        Route::post('/admission-year', [AdmissionYearController::class, 'store'])->name('admission-year.store');
        Route::get('/admission-year/{admissionYear}/edit', [AdmissionYearController::class, 'edit'])->name('admission-year.edit');
        Route::get('/admission-year/{admissionYear}', [AdmissionYearController::class, 'show'])->name('admission-year.show');
        Route::put('/admission-year/{admissionYear}', [AdmissionYearController::class, 'update'])->name('admission-year.update');
        Route::patch('/admission-year/{admissionYear}/inline', [AdmissionYearController::class, 'inlineUpdate'])->name('admission-year.inline-update');
        Route::delete('/admission-year/{admissionYear}', [AdmissionYearController::class, 'destroy'])->name('admission-year.destroy');
        Route::post('/admission-year/{admissionYear}/activate', [AdmissionYearController::class, 'activateAdmissionYear'])->name('admission-year.activate');
    });

    Route::middleware(EnsureRole::class.':admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Static segments first so they are not swallowed by /{staff}.
        Route::get('/staff/create', [StaffManagementController::class, 'create'])->name('staff.create');
        Route::get('/staff', [StaffManagementController::class, 'index'])->name('staff.index');
        Route::post('/staff', [StaffManagementController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [StaffManagementController::class, 'edit'])->name('staff.edit');
        Route::get('/staff/{staff}', [StaffManagementController::class, 'show'])->name('staff.show');
        Route::put('/staff/{staff}', [StaffManagementController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staff}', [StaffManagementController::class, 'destroy'])->name('staff.destroy');
        Route::post('/staff/{staff}/toggle-status', [StaffManagementController::class, 'toggleStatus'])->name('staff.toggle-status');
        Route::put('/staff/{staff}/permissions', [StaffManagementController::class, 'updatePermissions'])->name('staff.permissions.update');

        // Settings > Course Management. Static segments first so they are
        // not swallowed by /{course}.
        Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::patch('/courses/{course}/inline', [CourseController::class, 'inlineUpdate'])->name('courses.inline-update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

        // Settings > Document Types. Everything (add / rename / delete)
        // happens inline on the index page, no separate create/edit pages.
        Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('document-types.index');
        Route::post('/document-types', [DocumentTypeController::class, 'store'])->name('document-types.store');
        Route::patch('/document-types/{documentType}/inline', [DocumentTypeController::class, 'inlineUpdate'])->name('document-types.inline-update');
        Route::delete('/document-types/{documentType}', [DocumentTypeController::class, 'destroy'])->name('document-types.destroy');

        // Settings > Faculty Management. Renaming/deleting is admin-only;
        // adding a new faculty inline from the student form is also open to
        // staff with student permissions (see the shared route below).
        Route::get('/faculties', [FacultyController::class, 'index'])->name('faculties.index');
        Route::patch('/faculties/{faculty}/inline', [FacultyController::class, 'inlineUpdate'])->name('faculties.inline-update');
        Route::delete('/faculties/{faculty}', [FacultyController::class, 'destroy'])->name('faculties.destroy');

        // Settings > Institute Management. Same split as Faculty above.
        Route::get('/institutes', [InstituteController::class, 'index'])->name('institutes.index');
        Route::patch('/institutes/{institute}/inline', [InstituteController::class, 'inlineUpdate'])->name('institutes.inline-update');
        Route::delete('/institutes/{institute}', [InstituteController::class, 'destroy'])->name('institutes.destroy');
    });

    // Shared with student enrollment: any staff permitted to create/edit
    // students can add a new faculty/institute inline from the Academic step.
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('/faculties', [FacultyController::class, 'store'])->name('faculties.store');
        Route::post('/institutes', [InstituteController::class, 'store'])->name('institutes.store');
    });

    Route::middleware(EnsureRole::class.':staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'index'])->name('dashboard');
    });

    // Student Enrollment: usable by admin (always) or any staff member
    // granted the matching permission, unlike the admin-only groups above.
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/students/export/csv', [StudentController::class, 'exportCsv'])->name('students.export-csv')->middleware('can:students.export-csv');
        // Static segments first so they are not swallowed by /{student}.
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create')->middleware('can:students.create');
        Route::get('/students', [StudentController::class, 'index'])->name('students.index')->middleware('can:students.view');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store')->middleware('can:students.create');
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit')->middleware('can:students.edit');
        Route::get('/students/{student}/pdf', [StudentController::class, 'exportPdf'])->name('students.export-pdf')->middleware('can:students.export-pdf');
        Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show')->middleware('can:students.view');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update')->middleware('can:students.edit');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy')->middleware('can:students.delete');
        Route::delete('/students/{student}/documents/{document}', [StudentController::class, 'destroyDocument'])->name('students.documents.destroy')->middleware('can:students.edit');
    });
});
