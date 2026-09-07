@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Staff Banner Header -->
    <div class="bg-gradient-to-r from-[#232323] via-[#8D2229] to-[#971F20] text-white rounded-3xl p-6 sm:p-8 shadow-lg relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white p-0.5 border-2 border-[#971F20] overflow-hidden shadow-md shrink-0">
                    <img src="{{ $user->image_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-xl">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-400/30">
                            ● Active Staff
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight mt-1">
                        Welcome back, {{ $user->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-200 mt-0.5">
                        {{ $user->position }} • Informatics College Pokhara Admissions Team
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="px-4 py-2.5 bg-white text-[#8D2229] hover:bg-[#FBFBFB] hover:text-[#f53d3d] text-xs sm:text-sm font-bold rounded-xl shadow-md transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>New Student Enquiry</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Counselor Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Assigned Enquiries -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#676767] uppercase tracking-wider">Assigned Enquiries</span>
                <div class="p-2.5 rounded-xl bg-[#8D2229]/10 text-[#8D2229] border border-[#8D2229]/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#232323] tracking-tight">{{ $stats['assigned_enquiries'] }}</span>
                <span class="text-xs text-[#676767]">Students</span>
            </div>
            <div class="mt-3 text-xs text-[#676767] border-t border-slate-100 pt-3">
                Autumn 2026 Intake
            </div>
        </div>

        <!-- Pending Follow-ups -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#676767] uppercase tracking-wider">Pending Follow-ups</span>
                <div class="p-2.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-amber-700 tracking-tight">{{ $stats['pending_followups'] }}</span>
                <span class="text-xs text-amber-700 font-bold">Action Due</span>
            </div>
            <div class="mt-3 text-xs text-[#676767] border-t border-slate-100 pt-3">
                Calls / Campus visits
            </div>
        </div>

        <!-- Applications Processed -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#676767] uppercase tracking-wider">Applications Processed</span>
                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#232323] tracking-tight">{{ $stats['applications_processed'] }}</span>
                <span class="text-xs text-emerald-700 font-bold">Verified</span>
            </div>
            <div class="mt-3 text-xs text-[#676767] border-t border-slate-100 pt-3">
                Offers generated
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden group hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#676767] uppercase tracking-wider">Today's Appointments</span>
                <div class="p-2.5 rounded-xl bg-purple-50 text-purple-700 border border-purple-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#232323] tracking-tight">{{ $stats['today_appointments'] }}</span>
                <span class="text-xs text-[#676767]">Scheduled</span>
            </div>
            <div class="mt-3 text-xs text-[#676767] border-t border-slate-100 pt-3">
                First appointment at 11:00 AM
            </div>
        </div>
    </div>

    <!-- Assigned Student Enquiries Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-lg font-extrabold text-[#484848] tracking-tight">Recent Student Enquiries</h2>
                <p class="text-xs text-[#676767] mt-0.5">Assigned candidate inquiries for Wolverhampton UK Programs</p>
            </div>
            <span class="text-xs px-3 py-1 rounded-full bg-[#8D2229]/10 text-[#8D2229] border border-[#8D2229]/20 font-bold">
                Active Queue
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#484848]">
                <thead class="bg-[#232323] text-white text-xs uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-4">Enquiry ID</th>
                        <th class="px-6 py-4">Student Name</th>
                        <th class="px-6 py-4">Program Choice</th>
                        <th class="px-6 py-4">Phone Number</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($recentEnquiries as $enquiry)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-[#8D2229]">
                                {{ $enquiry['id'] }}
                            </td>
                            <td class="px-6 py-4 font-bold text-[#232323]">
                                {{ $enquiry['name'] }}
                            </td>
                            <td class="px-6 py-4 text-[#676767]">
                                {{ $enquiry['program'] }}
                            </td>
                            <td class="px-6 py-4 text-[#676767] font-mono text-xs">
                                {{ $enquiry['phone'] }}
                            </td>
                            <td class="px-6 py-4">
                                @if($enquiry['status'] === 'Offer Letter Sent')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        {{ $enquiry['status'] }}
                                    </span>
                                @elseif($enquiry['status'] === 'Documents Verified')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        {{ $enquiry['status'] }}
                                    </span>
                                @elseif($enquiry['status'] === 'Interview Scheduled')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                        {{ $enquiry['status'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        {{ $enquiry['status'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-xs text-[#676767] font-mono">
                                {{ $enquiry['date'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
