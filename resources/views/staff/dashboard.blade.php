@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')

{{-- ── Welcome Banner ── --}}
<div class="icp-welcome-banner mb-4 position-relative">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-sm-between gap-4 position-relative" style="z-index:1">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $user->image_url }}" alt="{{ $user->name }}" class="icp-avatar">
            <div>
                <div class="mb-1">
                    <span class="icp-badge" style="background:rgba(16,185,129,.18);color:#6ee7b7;border-color:rgba(16,185,129,.3)">
                        <span class="icp-badge-dot" style="background:#6ee7b7"></span>Active Staff
                    </span>
                </div>
                <h1 class="mb-0" style="font-size:1.6rem;font-weight:800;color:#fff;letter-spacing:-.5px">
                    Welcome back, {{ $user->name }}
                </h1>
                <p class="mb-0 mt-1" style="font-size:.82rem;color:rgba(255,255,255,.8)">
                    {{ $user->position }} • Informatics College Pokhara Admissions Team
                </p>
            </div>
        </div>
        <div>
            <button class="btn d-inline-flex align-items-center gap-2 px-4 py-2 fw-bold"
                style="background:#fff;color:var(--btn-color);border:none;border-radius:12px;font-size:.875rem;box-shadow:0 4px 12px rgba(0,0,0,.15);transition:all .2s">
                <i class="fas fa-plus"></i>
                <span>New Student Enquiry</span>
            </button>
        </div>
    </div>
</div>

{{-- ── Counselor Metrics Cards ── --}}
<div class="row g-4 mb-4">

    {{-- Assigned Enquiries --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="icp-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">Assigned Enquiries</span>
                <div class="stat-icon icp-icon-crimson">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-3">
                <span class="stat-value">{{ $stats['assigned_enquiries'] }}</span>
                <span class="stat-label" style="text-transform:none;letter-spacing:0">Students</span>
            </div>
            <div class="stat-sub">
                Autumn 2026 Intake
            </div>
        </div>
    </div>

    {{-- Pending Follow-ups --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="icp-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">Pending Follow-ups</span>
                <div class="stat-icon icp-icon-amber">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-3">
                <span class="stat-value" style="color:#d97706">{{ $stats['pending_followups'] }}</span>
                <span class="fw-bold" style="font-size:.75rem;color:#d97706">Action Due</span>
            </div>
            <div class="stat-sub">
                Calls / Campus visits
            </div>
        </div>
    </div>

    {{-- Applications Processed --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="icp-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">Applications Processed</span>
                <div class="stat-icon icp-icon-emerald">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-3">
                <span class="stat-value">{{ $stats['applications_processed'] }}</span>
                <span class="fw-bold" style="font-size:.75rem;color:#059669">Verified</span>
            </div>
            <div class="stat-sub">
                Offers generated
            </div>
        </div>
    </div>

    {{-- Today's Appointments --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="icp-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">Today's Appointments</span>
                <div class="stat-icon icp-icon-purple">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-3">
                <span class="stat-value">{{ $stats['today_appointments'] }}</span>
                <span class="stat-label" style="text-transform:none;letter-spacing:0">Scheduled</span>
            </div>
            <div class="stat-sub">
                First appointment at 11:00 AM
            </div>
        </div>
    </div>

</div>

{{-- ── Assigned Student Enquiries Table ── --}}
<div class="icp-card">
    <div class="icp-card-header d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-2">
        <div>
            <p class="icp-card-title">Recent Student Enquiries</p>
            <p class="icp-card-subtitle">Assigned candidate inquiries for Wolverhampton UK Programs</p>
        </div>
        <span class="icp-badge" style="background:rgba(141,34,41,.08);color:var(--btn-color);border:1px solid rgba(141,34,41,.15)">
            Active Queue
        </span>
    </div>

    <div class="table-responsive">
        <table class="table icp-table mb-0">
            <thead>
                <tr>
                    <th>Enquiry ID</th>
                    <th>Student Name</th>
                    <th>Program Choice</th>
                    <th>Phone Number</th>
                    <th>Status</th>
                    <th class="text-end">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentEnquiries as $enquiry)
                    <tr>
                        <td>
                            <span class="fw-bold" style="font-size:.78rem;color:var(--btn-color);font-family:monospace">
                                {{ $enquiry['id'] }}
                            </span>
                        </td>
                        <td class="fw-bold" style="color:#232323">{{ $enquiry['name'] }}</td>
                        <td style="color:var(--general-color)">{{ $enquiry['program'] }}</td>
                        <td style="color:var(--general-color);font-family:monospace;font-size:.8rem">{{ $enquiry['phone'] }}</td>
                        <td>
                            @if($enquiry['status'] === 'Offer Letter Sent')
                                <span class="icp-badge badge-success">{{ $enquiry['status'] }}</span>
                            @elseif($enquiry['status'] === 'Documents Verified')
                                <span class="icp-badge badge-info">{{ $enquiry['status'] }}</span>
                            @elseif($enquiry['status'] === 'Interview Scheduled')
                                <span class="icp-badge badge-purple">{{ $enquiry['status'] }}</span>
                            @else
                                <span class="icp-badge badge-pending">{{ $enquiry['status'] }}</span>
                            @endif
                        </td>
                        <td class="text-end" style="font-size:.78rem;color:var(--general-color);font-family:monospace">
                            {{ $enquiry['date'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
