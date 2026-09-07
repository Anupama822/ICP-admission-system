<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#FBFBFB] font-sans text-[#676767] antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Informatics College Pokhara Admission Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FBFBFB;
            color: #676767;
        }
    </style>
</head>
<body class="min-h-screen bg-[#FBFBFB] flex flex-col justify-between selection:bg-[#971F20] selection:text-white">

    <div class="min-h-screen flex flex-col lg:flex-row w-full">
        <!-- Left Side: Image & Branding Banner (hidden on mobile, visible on lg screens) -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-[#232323] flex-col justify-between p-8 lg:p-12 overflow-hidden min-h-screen">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/login-banner.jpg') }}" alt="Informatics College Pokhara Campus" class="w-full h-full object-cover object-center opacity-40">
                <div class="absolute inset-0 bg-gradient-to-t from-[#232323] via-[#232323]/70 to-[#8D2229]/60"></div>
            </div>

            <!-- Top Logo & College Name -->
            <div class="relative z-10 flex items-center gap-4">
                <div class="p-2 bg-white rounded-2xl shadow-lg border border-white/20">
                    <img src="{{ asset('images/icp-logo.png') }}" alt="ICP Logo" class="h-12 w-auto object-contain" />
                </div>
            </div>

            <!-- Middle Feature Text -->
            <div class="relative z-10 my-auto max-w-lg space-y-4 py-12">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#8D2229] text-white text-xs font-bold tracking-wide shadow-md">
                    UK Higher Education in Nepal
                </span>
                <h1 class="text-3xl lg:text-4xl font-extrabold text-white leading-tight tracking-tight">
                    Empowering Your Future in IT & Business
                </h1>
                <p class="text-sm text-slate-300 leading-relaxed font-medium">
                    Admission Management Portal for Informatics College Pokhara. Seamlessly manage student enquiries, application reviews, and enrollment workflows.
                </p>

                <!-- Affiliation Badge -->
                <div class="pt-4 flex items-center gap-3 border-t border-white/10">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#f53d3d] animate-pulse"></div>
                    <span class="text-xs text-slate-200 font-semibold">University Partner - London Metropolitan University, UK</span>
                </div>
            </div>

            <!-- Footer info on banner side -->
            <div class="relative z-10 text-xs text-slate-400">
                <p>&copy; {{ date('Y') }} Informatics College Pokhara. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-between p-6 sm:p-12 lg:p-16 bg-[#FBFBFB] min-h-screen">
            <!-- Mobile Top Logo (shown only on small screens) -->
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <img src="{{ asset('images/icp-logo.png') }}" alt="ICP Logo" class="h-12 w-auto" />
                <div>
                    <h2 class="font-extrabold text-[#232323] text-lg leading-tight">Informatics College</h2>
                    <p class="text-xs text-[#971F20] font-bold">POKHARA, NEPAL</p>
                </div>
            </div>

            <div class="max-w-md w-full mx-auto my-auto py-6">
                <!-- Header section -->
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-[#9F0D1A] tracking-tight">Welcome Back</h2>
                    <p class="text-sm text-[#676767] mt-1.5 font-medium">Please sign in to access the Admission Management System</p>
                </div>

                <!-- Error Alerts (Inactive Account / Invalid Credentials) -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-start gap-2.5">
                                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span class="font-semibold text-xs sm:text-sm leading-tight text-rose-900">{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-xs sm:text-sm font-medium">{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#484848] mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="Enter Email Address"
                                class="w-full pl-10 pr-4 py-3.5 bg-white border border-slate-300 rounded-xl text-[#232323] text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#971F20] focus:border-transparent transition-all shadow-xs">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#484848] mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" type="password" name="password" required
                                placeholder="Enter Password"
                                class="w-full pl-10 pr-4 py-3.5 bg-white border border-slate-300 rounded-xl text-[#232323] text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#971F20] focus:border-transparent transition-all shadow-xs">
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-[#8D2229] focus:ring-[#971F20]">
                            <span class="text-xs text-[#676767] font-medium">Keep me signed in</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-3.5 px-4 bg-[#8D2229] hover:bg-[#f53d3d] text-white font-bold text-sm rounded-xl shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-[#971F20] active:scale-[0.99] flex items-center justify-center gap-2">
                        <span>Sign In to Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Footer for Form side on small screens -->
            <div class="lg:hidden text-center text-xs text-[#676767] pt-6">
                <p>&copy; {{ date('Y') }} Informatics College Pokhara. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
