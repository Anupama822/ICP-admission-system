<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    /**
     * Display Staff Dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'assigned_enquiries' => 38,
            'pending_followups' => 12,
            'applications_processed' => 29,
            'today_appointments' => 5,
        ];

        $recentEnquiries = [
            [
                'id' => 'ENQ-2026-089',
                'name' => 'Bipasha Thapa',
                'program' => 'BSc (Hons) Computer Science',
                'phone' => '+977 9846012345',
                'status' => 'Pending Review',
                'date' => '2026-09-07',
            ],
            [
                'id' => 'ENQ-2026-090',
                'name' => 'Rohan Gurung',
                'program' => 'BBA (Hons) International Business',
                'phone' => '+977 9806123456',
                'status' => 'Interview Scheduled',
                'date' => '2026-09-07',
            ],
            [
                'id' => 'ENQ-2026-091',
                'name' => 'Sneha Adhikari',
                'program' => 'BSc (Hons) Computer Science',
                'phone' => '+977 9812345678',
                'status' => 'Documents Verified',
                'date' => '2026-09-06',
            ],
            [
                'id' => 'ENQ-2026-092',
                'name' => 'Prashant Sharma',
                'program' => 'BIT (Hons) Software Engineering',
                'phone' => '+977 9856034567',
                'status' => 'Offer Letter Sent',
                'date' => '2026-09-05',
            ],
        ];

        return view('staff.dashboard', compact('user', 'stats', 'recentEnquiries'));
    }
}
