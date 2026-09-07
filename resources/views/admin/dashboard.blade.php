@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-[#971F20] uppercase tracking-wider mb-1">
                <span>System Administration</span>
                <span>•</span>
                <span>Informatics College Pokhara</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#9F0D1A] tracking-tight">Admissions Control Center</h1>
            <p class="text-sm text-[#676767] mt-1">Manage staff accounts, monitor admission metrics, and oversee system operations.</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('addStaffModal').classList.remove('hidden')" 
                class="px-4 py-2.5 bg-[#8D2229] hover:bg-[#f53d3d] text-white text-sm font-bold rounded-xl shadow-md transition-all flex items-center gap-2 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Add New Staff</span>
            </button>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Staff -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#676767] uppercase tracking-wider">Total Staff</span>
                <div class="p-2.5 rounded-xl bg-[#8D2229]/10 text-[#8D2229] border border-[#8D2229]/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#232323] tracking-tight">{{ $stats['total_staff'] }}</span>
                <span class="text-xs text-[#676767]">Counselors</span>
            </div>
            <div class="mt-3 flex items-center gap-3 text-xs border-t border-slate-100 pt-3 font-semibold">
                <span class="text-emerald-700">{{ $stats['active_staff'] }} Active</span>
                <span class="text-slate-300">•</span>
                <span class="text-rose-700">{{ $stats['inactive_staff'] }} Inactive</span>
            </div>
        </div>

        <!-- Total Applications -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#676767] uppercase tracking-wider">Total Applications</span>
                <div class="p-2.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#232323] tracking-tight">{{ $stats['total_applications'] }}</span>
                <span class="text-xs text-emerald-700 font-bold">↑ 14% this month</span>
            </div>
            <div class="mt-3 text-xs text-[#676767] border-t border-slate-100 pt-3">
                Academic Session 2026
            </div>
        </div>

        <!-- Approved Admissions -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#676767] uppercase tracking-wider">Approved Admissions</span>
                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#232323] tracking-tight">{{ $stats['approved_admissions'] }}</span>
                <span class="text-xs text-[#676767]">Enrolled</span>
            </div>
            <div class="mt-3 text-xs text-[#676767] border-t border-slate-100 pt-3">
                BSc CS (84) • BBA (58)
            </div>
        </div>

        <!-- Pending Verifications -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#676767] uppercase tracking-wider">Pending Review</span>
                <div class="p-2.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#232323] tracking-tight">{{ $stats['pending_verifications'] }}</span>
                <span class="text-xs text-amber-700 font-bold">Action Due</span>
            </div>
            <div class="mt-3 text-xs text-[#676767] border-t border-slate-100 pt-3">
                Assigned across staff
            </div>
        </div>
    </div>

    <!-- Staff Management Section -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
            <div>
                <h2 class="text-lg font-extrabold text-[#484848] tracking-tight">Staff Account Management</h2>
                <p class="text-xs text-[#676767] mt-0.5">Staff accounts are created by Admin. Inactive staff members cannot log in.</p>
            </div>
            <span class="text-xs px-3 py-1 rounded-full bg-white text-[#232323] border border-slate-200 font-bold shadow-xs">
                Total Staff: {{ $staffMembers->count() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#484848]">
                <thead class="bg-[#232323] text-white text-xs uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-4">Staff Member</th>
                        <th class="px-6 py-4">Position</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($staffMembers as $staff)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-300 overflow-hidden shrink-0">
                                        <img src="{{ $staff->image_url }}" alt="{{ $staff->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#232323]">{{ $staff->name }}</div>
                                        <div class="text-xs text-[#676767] font-mono">{{ $staff->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[#676767]">
                                {{ $staff->position }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($staff->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                        Inactive (Blocked)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.staff.toggle-status', $staff->id) }}" class="inline">
                                    @csrf
                                    @if ($staff->status === 'active')
                                        <button type="submit" 
                                            class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition focus:outline-none"
                                            title="Deactivate staff (login will be denied)">
                                            Deactivate
                                        </button>
                                    @else
                                        <button type="submit" 
                                            class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold transition focus:outline-none"
                                            title="Activate staff (login will be allowed)">
                                            Activate Login
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-[#676767] text-sm">
                                No staff members found. Click "Add New Staff" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Academic Programs & Logs Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Programs Offered -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-base font-extrabold text-[#9F0D1A] tracking-tight">Active Academic Programs</h3>
            <div class="space-y-3">
                <div class="p-3.5 rounded-xl bg-[#FBFBFB] border border-slate-200 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-[#232323] text-sm">BSc (Hons) Computer Science</div>
                        <div class="text-xs text-[#676767]">4 Years • Univ. of Wolverhampton</div>
                    </div>
                    <span class="text-xs font-bold text-[#8D2229] bg-[#8D2229]/10 px-2.5 py-1 rounded-full border border-[#8D2229]/20">84 Enrolled</span>
                </div>
                <div class="p-3.5 rounded-xl bg-[#FBFBFB] border border-slate-200 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-[#232323] text-sm">BBA (Hons) International Business</div>
                        <div class="text-xs text-[#676767]">4 Years • Univ. of Wolverhampton</div>
                    </div>
                    <span class="text-xs font-bold text-[#971F20] bg-[#971F20]/10 px-2.5 py-1 rounded-full border border-[#971F20]/20">58 Enrolled</span>
                </div>
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-base font-extrabold text-[#484848] tracking-tight">Recent System Logs</h3>
            <div class="space-y-3 text-xs">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-[#FBFBFB] border border-slate-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 shrink-0"></span>
                    <span class="text-[#232323]">Staff <strong>Aarav Shrestha</strong> approved application #ENQ-2026-091 (Sneha Adhikari)</span>
                    <span class="ml-auto text-[#676767] font-mono">10 mins ago</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-[#FBFBFB] border border-slate-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#8D2229] shrink-0"></span>
                    <span class="text-[#232323]">New student enquiry received for <strong>BSc (Hons) Computer Science</strong></span>
                    <span class="ml-auto text-[#676767] font-mono">42 mins ago</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-[#FBFBFB] border border-slate-200">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#971F20] shrink-0"></span>
                    <span class="text-[#232323]">Admin <strong>System Administrator</strong> updated staff status</span>
                    <span class="ml-auto text-[#676767] font-mono">1 hour ago</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add New Staff -->
<div id="addStaffModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs hidden p-4">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <div>
                <h3 class="text-lg font-extrabold text-[#9F0D1A]">Add New Staff Member</h3>
                <p class="text-xs text-[#676767]">Create a staff account for Informatics College Pokhara</p>
            </div>
            <button onclick="document.getElementById('addStaffModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#484848] mb-1">Status</label>
                <select name="status" required
                    class="w-full px-3 py-2.5 bg-[#FBFBFB] border border-slate-300 rounded-xl text-[#232323] text-sm focus:ring-2 focus:ring-[#971F20] focus:outline-none">
                    <option value="active">Active (Login Allowed)</option>
                    <option value="inactive">Inactive (Login Denied)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#484848] mb-1">Full Name</label>
                <input type="text" name="name" required placeholder="e.g. Maya Gurung"
                    class="w-full px-3 py-2.5 bg-[#FBFBFB] border border-slate-300 rounded-xl text-[#232323] text-sm focus:ring-2 focus:ring-[#971F20] focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#484848] mb-1">Email Address</label>
                <input type="email" name="email" required placeholder="e.g. maya@icp.edu.np"
                    class="w-full px-3 py-2.5 bg-[#FBFBFB] border border-slate-300 rounded-xl text-[#232323] text-sm focus:ring-2 focus:ring-[#971F20] focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#484848] mb-1">Position / Role</label>
                <input type="text" name="position" required placeholder="e.g. Admissions Counselor"
                    class="w-full px-3 py-2.5 bg-[#FBFBFB] border border-slate-300 rounded-xl text-[#232323] text-sm focus:ring-2 focus:ring-[#971F20] focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#484848] mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••••••"
                    class="w-full px-3 py-2.5 bg-[#FBFBFB] border border-slate-300 rounded-xl text-[#232323] text-sm focus:ring-2 focus:ring-[#971F20] focus:outline-none">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200">
                <button type="button" onclick="document.getElementById('addStaffModal').classList.add('hidden')"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-[#676767] text-sm font-bold rounded-xl transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2 bg-[#8D2229] hover:bg-[#f53d3d] text-white text-sm font-bold rounded-xl shadow-md transition">
                    Create Staff Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
