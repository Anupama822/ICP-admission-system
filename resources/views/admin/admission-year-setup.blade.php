@extends('layouts.app')

@section('title', 'Set Up Admission Year')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-6 sm:px-8 border-b border-slate-100">
            <div class="flex items-start gap-4">

                <div class="w-11 h-11 rounded-xl bg-[#8D2229]/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#8D2229]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>

                <div>
                    <p class="text-xs font-bold text-[#971F20] uppercase tracking-wider">
                        Initial setup
                    </p>

                    <h1 class="text-2xl font-extrabold text-[#9F0D1A] tracking-tight mt-1">
                        Set up the admission year
                    </h1>

                    <p class="text-sm text-[#676767] mt-1">
                        Create the admission year that will be active for the system.
                    </p>
                </div>

            </div>
        </div>


        {{-- Form --}}
        <div class="p-6 sm:p-8">

            <form method="POST" action="{{ route('admin.admission-year.store') }}">
                @csrf

                {{-- Fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Admission Year Title --}}
                    <div>
                        <label for="title"
                               class="block text-sm font-semibold text-[#484848] mb-2">
                            Admission year title
                        </label>

                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title', date('Y')) }}"
                            placeholder="e.g. 2026/27"
                            required
                            autofocus
                            class="w-full h-11 px-4 rounded-lg border border-slate-300
                                   text-sm text-slate-700
                                   placeholder:text-slate-400
                                   focus:border-[#8D2229]
                                   focus:ring-2 focus:ring-[#8D2229]/10
                                   outline-none transition"
                        >

                        @error('title')
                            <p class="text-xs text-rose-600 mt-1.5">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- Starting Year --}}
                    <div>
                        <label for="year"
                               class="block text-sm font-semibold text-[#484848] mb-2">
                            Starting year
                        </label>

                        <input
                            id="year"
                            name="year"
                            type="number"
                            value="{{ old('year', date('Y')) }}"
                            placeholder="e.g. 2026"
                            min="2000"
                            max="2100"
                            required
                            class="w-full h-11 px-4 rounded-lg border border-slate-300
                                   text-sm text-slate-700
                                   placeholder:text-slate-400
                                   focus:border-[#8D2229]
                                   focus:ring-2 focus:ring-[#8D2229]/10
                                   outline-none transition"
                        >

                        @error('year')
                            <p class="text-xs text-rose-600 mt-1.5">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>


                {{-- Info --}}
                <div class="mt-6 flex gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <svg class="w-5 h-5 text-[#8D2229] shrink-0 mt-0.5"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"/>
                    </svg>

                    <p class="text-xs leading-5 text-slate-600">
                        This admission year will be used as the active academic period
                        throughout the admission management system.
                    </p>
                </div>


                {{-- Action --}}
                <div class="mt-7 flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2
                               px-6 py-2.5
                               bg-[#8D2229]
                               hover:bg-[#721b21]
                               active:bg-[#60171c]
                               text-white text-sm font-semibold
                               rounded-lg shadow-sm
                               transition-all duration-200
                               focus:outline-none focus:ring-2
                               focus:ring-[#8D2229]/30"
                    >
                        Set active admission year

                        <svg class="w-4 h-4"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
