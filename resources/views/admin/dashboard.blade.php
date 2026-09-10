@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

{{-- ── Page Header ── --}}
<div class="icp-page-header d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3 my-4">
    <div>
        <div class="icp-breadcrumb">
            <span>System Administration</span>
            <span class="mx-1">•</span>
            <span>Informatics College Pokhara</span>
        </div>
        <h1 class="mb-1">Admissions Control Center</h1>
        <p class="icp-page-subtitle mb-0">Manage staff accounts, monitor admission metrics, and oversee system operations.</p>
        <div class="mt-2">
            <span class="icp-year-pill">
                <span class="icp-year-dot"></span>
                Active academic year: {{ $activeAcademicYear?->title ?? 'Not set' }}
            </span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.staff.index') }}" class="btn icp-btn-muted d-inline-flex align-items-center gap-2 px-4 py-2">
            <i class="fas fa-users"></i>
            <span>Manage Staff</span>
        </a>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
            <i class="fas fa-user-plus"></i>
            <span>Add New Staff</span>
        </a>
    </div>
</div>

{{-- ── Statistics Grid ── --}}
<div class="row g-4 mb-4">

    {{-- Total Staff --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="icp-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">Total Staff</span>
                <div class="stat-icon icp-icon-crimson">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-3">
                <span class="stat-value">{{ $stats['total_staff'] }}</span>
                <span class="stat-label" style="text-transform:none;letter-spacing:0">Counselors</span>
            </div>
            <div class="stat-sub d-flex gap-3">
                <span class="text-success fw-semibold">{{ $stats['active_staff'] }} Active</span>
                <span class="text-muted">•</span>
                <span class="text-danger fw-semibold">{{ $stats['inactive_staff'] }} Inactive</span>
            </div>
        </div>
    </div>

    {{-- Total Applications --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="icp-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">Total Applications</span>
                <div class="stat-icon icp-icon-blue">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-3">
                <span class="stat-value">{{ $stats['total_applications'] }}</span>
                <span class="text-success fw-bold" style="font-size:.75rem">↑ 14% this month</span>
            </div>
            <div class="stat-sub">
                Academic Session 2026
            </div>
        </div>
    </div>

    {{-- Approved Admissions --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="icp-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">Approved Admissions</span>
                <div class="stat-icon icp-icon-emerald">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-3">
                <span class="stat-value">{{ $stats['approved_admissions'] }}</span>
                <span class="stat-label" style="text-transform:none;letter-spacing:0">Enrolled</span>
            </div>
            <div class="stat-sub">
                BSc CS (84) • BBA (58)
            </div>
        </div>
    </div>

    {{-- Pending Review --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="icp-stat-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">Pending Review</span>
                <div class="stat-icon icp-icon-amber">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-3">
                <span class="stat-value" style="color: #d97706">{{ $stats['pending_verifications'] }}</span>
                <span class="fw-bold" style="font-size:.75rem;color:#d97706">Action Due</span>
            </div>
            <div class="stat-sub">
                Assigned across staff
            </div>
        </div>
    </div>

</div>

{{-- ── Bottom Row: Programs + Logs ── --}}
<div class="row g-4">

    {{-- Academic Programs --}}
    <div class="col-12 col-lg-4">
        <div class="icp-card h-100">
            <div class="icp-card-header">
                <p class="icp-card-title" style="color:var(--highlight-heading-color)">Active Academic Programs</p>
            </div>
            <div class="p-3 d-flex flex-column gap-3">

                <div class="p-3 rounded-3 d-flex align-items-center justify-content-between"
                    style="background:var(--background-color);border:1px solid #f0e6e7">
                    <div>
                        <div class="fw-bold" style="font-size:.875rem;color:#232323">BSc (Hons) Computer Science</div>
                        <div style="font-size:.75rem;color:var(--general-color)">4 Years • Univ. of Wolverhampton</div>
                    </div>
                    <span class="icp-badge" style="background:rgba(141,34,41,.08);color:var(--btn-color);border:1px solid rgba(141,34,41,.15)">84 Enrolled</span>
                </div>

                <div class="p-3 rounded-3 d-flex align-items-center justify-content-between"
                    style="background:var(--background-color);border:1px solid #f0e6e7">
                    <div>
                        <div class="fw-bold" style="font-size:.875rem;color:#232323">BBA (Hons) International Business</div>
                        <div style="font-size:.75rem;color:var(--general-color)">4 Years • Univ. of Wolverhampton</div>
                    </div>
                    <span class="icp-badge" style="background:rgba(151,31,32,.08);color:var(--highlight-color);border:1px solid rgba(151,31,32,.15)">58 Enrolled</span>
                </div>

            </div>
        </div>
    </div>

    {{-- Recent System Logs --}}
    <div class="col-12 col-lg-8">
        <div class="icp-card h-100">
            <div class="icp-card-header">
                <p class="icp-card-title">Recent System Logs</p>
            </div>
            <div class="p-3 d-flex flex-column gap-2">

                <div class="icp-log-item">
                    <span class="log-dot" style="background:#10b981"></span>
                    <span style="color:#232323">Staff <strong>Aarav Shrestha</strong> approved application #ENQ-2026-091 (Sneha Adhikari)</span>
                    <span class="log-time">10 mins ago</span>
                </div>

                <div class="icp-log-item">
                    <span class="log-dot" style="background:#8D2229"></span>
                    <span style="color:#232323">New student enquiry received for <strong>BSc (Hons) Computer Science</strong></span>
                    <span class="log-time">42 mins ago</span>
                </div>

                <div class="icp-log-item">
                    <span class="log-dot" style="background:#971F20"></span>
                    <span style="color:#232323">Admin <strong>System Administrator</strong> updated staff status</span>
                    <span class="log-time">1 hour ago</span>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
