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
    <div>
        <button data-bs-toggle="modal" data-bs-target="#addStaffModal"
            class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
            <i class="fas fa-user-plus"></i>
            <span>Add New Staff</span>
        </button>
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

{{-- ── Staff Management Table ── --}}
<div class="icp-card mb-4">
    <div class="icp-card-header d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-2">
        <div>
            <p class="icp-card-title">Staff Account Management</p>
            <p class="icp-card-subtitle">Staff accounts are created by Admin. Inactive staff members cannot log in.</p>
        </div>
        <span class="icp-badge badge-active" style="font-size:.72rem">
            Total Staff: {{ $staffMembers->count() }}
        </span>
    </div>
    <div class="table-responsive">
        <table class="table icp-table mb-0">
            <thead>
                <tr>
                    <th>Staff Member</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($staffMembers as $staff)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $staff->image_url }}" alt="{{ $staff->name }}"
                                    class="rounded-circle border"
                                    style="width:40px;height:40px;object-fit:cover;border-color:#f0e6e7 !important">
                                <div>
                                    <div class="fw-bold" style="color:#232323">{{ $staff->name }}</div>
                                    <div style="font-size:.78rem;color:var(--general-color);font-family:monospace">{{ $staff->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--general-color)">{{ $staff->position }}</td>
                        <td>
                            @if ($staff->status === 'active')
                                <span class="icp-badge badge-active">
                                    <span class="icp-badge-dot"></span>Active
                                </span>
                            @else
                                <span class="icp-badge badge-inactive">
                                    <span class="icp-badge-dot"></span>Inactive (Blocked)
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.staff.toggle-status', $staff->id) }}" class="d-inline">
                                @csrf
                                @if ($staff->status === 'active')
                                    <button type="submit" class="btn btn-sm"
                                        style="background:rgba(239,68,68,.08);color:#b91c1c;border:1px solid rgba(239,68,68,.2);border-radius:8px;font-size:.78rem;font-weight:700"
                                        title="Deactivate staff (login will be denied)">
                                        Deactivate
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm"
                                        style="background:rgba(16,185,129,.08);color:#065f46;border:1px solid rgba(16,185,129,.2);border-radius:8px;font-size:.78rem;font-weight:700"
                                        title="Activate staff (login will be allowed)">
                                        Activate Login
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4" style="color:var(--general-color)">
                            <i class="fas fa-users mb-2 d-block" style="font-size:1.5rem;opacity:.3"></i>
                            No staff members found. Click "Add New Staff" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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

{{-- ── Add New Staff Modal ── --}}
<div class="modal fade icp-modal" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="addStaffModalLabel">Add New Staff Member</h5>
                    <p class="mb-0 small" style="color:var(--general-color)">Create a staff account for Informatics College Pokhara</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('admin.staff.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small" style="color:var(--heading-color)">Status</label>
                        <select name="status" required class="form-select"
                            style="border-color:#e5e7eb;border-radius:10px;font-size:.9rem">
                            <option value="active">Active (Login Allowed)</option>
                            <option value="inactive">Inactive (Login Denied)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small" style="color:var(--heading-color)">Full Name</label>
                        <input type="text" name="name" required placeholder="e.g. Maya Gurung"
                            class="form-control" style="border-color:#e5e7eb;border-radius:10px;font-size:.9rem">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small" style="color:var(--heading-color)">Email Address</label>
                        <input type="email" name="email" required placeholder="e.g. maya@icp.edu.np"
                            class="form-control" style="border-color:#e5e7eb;border-radius:10px;font-size:.9rem">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small" style="color:var(--heading-color)">Position / Role</label>
                        <input type="text" name="position" required placeholder="e.g. Admissions Counselor"
                            class="form-control" style="border-color:#e5e7eb;border-radius:10px;font-size:.9rem">
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold small" style="color:var(--heading-color)">Password</label>
                        <input type="password" name="password" required placeholder="••••••••••••"
                            class="form-control" style="border-color:#e5e7eb;border-radius:10px;font-size:.9rem">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm px-4 py-2 fw-bold" data-bs-dismiss="modal"
                        style="background:#f3f4f6;color:var(--general-color);border:none;border-radius:10px">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 py-2">
                        Create Staff Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
