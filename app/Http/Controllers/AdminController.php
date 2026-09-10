<?php

namespace App\Http\Controllers;

use App\Models\AdmissionYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{

    /**
     * Display Admin Dashboard.
     */
    public function index()
    {
        $staffMembers = User::where('role', 'staff')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeAcademicYear = AdmissionYear::where('is_active', true)->first();
        $academicYears = AdmissionYear::orderBy('year', 'desc')->get();

        $stats = [
            'total_staff' => $staffMembers->count(),
            'active_staff' => $staffMembers->where('status', 'active')->count(),
            'inactive_staff' => $staffMembers->where('status', 'inactive')->count(),
            'total_applications' => 184,
            'approved_admissions' => 142,
            'pending_verifications' => 42,
        ];

        return view('admin.dashboard', compact('staffMembers', 'stats', 'activeAcademicYear', 'academicYears'));
    }

    /**
     * Store a newly created staff member.
     */
    public function storeStaff(Request $request)
    {
        $activeAcademicYear = AdmissionYear::where('is_active', true)->first();

        if (!$activeAcademicYear) {
            return back()->withErrors(['error' => 'Set an active academic year before creating new records.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'position' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $validated['role'] = 'staff';
        $validated['password'] = Hash::make($validated['password']);
        $validated['image_url'] = 'https://ui-avatars.com/api/?name=' . urlencode($validated['name']) . '&background=0284c7&color=fff';

        User::create($validated);

        return redirect()->route('admin.dashboard')->with('status', 'Staff member "' . $validated['name'] . '" added successfully!');
    }

    /**
     * Toggle active/inactive status of a staff member.
     */
    public function toggleStaffStatus(User $user)
    {
        if ($user->role !== 'staff') {
            return back()->withErrors(['error' => 'Cannot modify administrator status.']);
        }

        $user->status = ($user->status === 'active') ? 'inactive' : 'active';
        $user->save();

        $statusText = ucfirst($user->status);
        return redirect()->route('admin.dashboard')->with('status', "Staff member {$user->name} status changed to {$statusText}.");
    }
}
