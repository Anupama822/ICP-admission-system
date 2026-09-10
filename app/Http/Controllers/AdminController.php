<?php

namespace App\Http\Controllers;

use App\Models\AdmissionYear;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Display Admin Dashboard.
     */
    public function index()
    {
        $activeAcademicYear = AdmissionYear::where('is_active', true)->first();
        $academicYears = AdmissionYear::orderBy('year', 'desc')->get();

        $staffQuery = User::where('role', 'staff');

        $stats = [
            'total_staff' => (clone $staffQuery)->count(),
            'active_staff' => (clone $staffQuery)->where('status', 'active')->count(),
            'inactive_staff' => (clone $staffQuery)->where('status', 'inactive')->count(),
            'total_applications' => 184,
            'approved_admissions' => 142,
            'pending_verifications' => 42,
        ];

        return view('admin.dashboard', compact('stats', 'activeAcademicYear', 'academicYears'));
    }
}
