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
                <div class="flex items-center gap-4">
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

        @if ($errors->any())
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
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        @yield('content')
    </main>

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
