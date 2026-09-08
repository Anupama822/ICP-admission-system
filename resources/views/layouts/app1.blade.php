<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#FBFBFB] font-sans text-[#676767] antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admission Management System') - Informatics College Pokhara</title>
    <meta name="description" content="Informatics College Pokhara - Admission Management System">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Fav Icon -->
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="manifest" href="/site.webmanifest" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--background-color, #FBFBFB);
            color: var(--general-color, #676767);
        }
        h1, h2, h3, h4, h5, h6 {
            color: var(--heading-color, #484848);
        }
        .highlight-heading {
            color: var(--highlight-heading-color, #9F0D1A);
        }
    </style>
</head>
<body class="h-full bg-[#FBFBFB] text-[#676767] flex flex-col min-h-screen selection:bg-[#971F20] selection:text-white">
    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-[#232323] text-white shadow-md border-b border-[#971F20]/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Branding -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#8D2229] flex items-center justify-center font-extrabold text-white text-lg shadow-md border border-[#971F20]">
                        <img src="{{asset('images/logo.png')}}" alt="ICP Logo" class="w-full h-full object-contain rounded-xl">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold tracking-tight text-white text-lg">Informatics College Pokhara</span>
                        </div>
                        <p class="text-xs text-slate-300">Admission Management System</p>
                    </div>
                </div>

                <!-- User Actions -->
                @auth
                @php
                    $activeAcademicYear = \App\Models\AdmissionYear::where('is_active', true)->first();
                @endphp
                <div class="flex items-center gap-3">
                    @if(Auth::user()->isAdmin())
                        <details class="relative">
                            <summary class="list-none flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-left transition hover:bg-white/10">
                                <span class="hidden sm:inline text-[10px] font-bold uppercase tracking-[0.2em] text-slate-300">Academic year</span>
                                <span class="text-sm font-bold text-white">{{ $activeAcademicYear?->title ?? 'Set year' }}</span>
                                <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </summary>

                            <div class="absolute right-0 top-full z-50 mt-2 w-72 rounded-2xl border border-slate-200 bg-white p-2 shadow-2xl">
                                <div class="px-3 py-2 border-b border-slate-100">
                                    <a href="{{ route('admission-year.create') }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-semibold text-[#232323] hover:bg-slate-50">
                                        <span>Create new academic year</span>
                                        <svg class="h-4 w-4 text-[#8D2229]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8h-16" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </details>
                    @else
                        <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-left">
                            <span class="hidden sm:inline text-[10px] font-bold uppercase tracking-[0.2em] text-slate-300">Academic year</span>
                            <span class="text-sm font-bold text-white">{{ $activeAcademicYear?->title ?? 'Not set' }}</span>
                        </div>
                    @endif

                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-sm font-semibold text-white">{{ Auth::user()->name }}</span>
                        <div class="flex items-center gap-1.5">
                            @if(Auth::user()->isAdmin())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-[#9F0D1A] text-white">
                                    Administrator
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-[#8D2229] text-white">
                                    Staff ({{ Auth::user()->position }})
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="w-9 h-9 rounded-full bg-[#484848] border-2 border-[#8D2229] overflow-hidden flex items-center justify-center">
                        @if(Auth::user()->image_url)
                            <img src="{{ Auth::user()->image_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-sm font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-[#8D2229] transition-colors focus:outline-none" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Alert Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            </div>
        @endif

        <!-- @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1 shadow-sm">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif -->
    </div>

    <!-- Main Content -->
    @if(Auth::check())
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
                <aside class="w-full lg:w-72 shrink-0">
                    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#971F20]">Navigation</p>
                            <h2 class="mt-2 text-lg font-extrabold text-[#484848]">Workspace</h2>
                        </div>

                        <nav class="p-3">
                            <ul class="space-y-1.5">
                                @if(Auth::user()->isAdmin())
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-[#484848] transition hover:bg-[#8D2229]/5 hover:text-[#8D2229] {{ request()->routeIs('admin.dashboard') ? 'bg-[#8D2229]/5 text-[#8D2229]' : '' }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10h14V10" /></svg>
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admission-year.setup') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-[#484848] transition hover:bg-[#8D2229]/5 hover:text-[#8D2229] {{ request()->routeIs('admission-year.setup') ? 'bg-[#8D2229]/5 text-[#8D2229]' : '' }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            Academic Years
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-[#484848] transition hover:bg-[#8D2229]/5 hover:text-[#8D2229]">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            Staff
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-[#484848] transition hover:bg-[#8D2229]/5 hover:text-[#8D2229] {{ request()->routeIs('staff.dashboard') ? 'bg-[#8D2229]/5 text-[#8D2229]' : '' }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10h14V10" /></svg>
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-[#484848] transition hover:bg-[#8D2229]/5 hover:text-[#8D2229]">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            Enquiries
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </aside>

                <div class="flex-1">
                    <main class="w-full">
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    @else
        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
            @yield('content')
        </main>
    @endif

    <!-- Footer -->
    <footer class="bg-[#232323] text-slate-300 border-t-4 border-[#8D2229] py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs gap-4">
            <p>&copy; {{ date('Y') }} <span class="font-bold text-white">Informatics College Pokhara</span>. Affiliated with University of Wolverhampton, UK. All rights reserved.</p>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Admission Management Portal</span>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
