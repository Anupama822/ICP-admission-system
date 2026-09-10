@extends('layouts.app')

@section('title', 'Set Up Admission Year')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-7">

        <div class="icp-form-card">

            {{-- Header --}}
            <div class="icp-form-header d-flex align-items-center gap-3">
                <div class="form-icon">
                    @svg('heroicon-o-calendar-days', 'icp-icon-lg')
                </div>
                <div>
                    <p class="mb-0" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--highlight-color)">
                        Initial Setup
                    </p>
                    <h1 style="font-size:1.4rem;font-weight:800;color:var(--highlight-heading-color);margin:.15rem 0 0;letter-spacing:-.3px">
                        Set up the Admission Year
                    </h1>
                    <p class="mb-0 small" style="color:var(--general-color);margin-top:.2rem">
                        Create the admission year that will be active for the system.
                    </p>
                </div>
            </div>

            {{-- Form --}}
            <div class="icp-form-body">
                <form method="POST" action="{{ route('admission-year.setup.store') }}">
                    @csrf

                    @include('admin.templates.partials.errors')

                    <div class="row g-4 mb-4">

                        {{-- Admission Year Title --}}
                        <div class="col-12 col-sm-6">
                            <label for="title" class="form-label fw-semibold small" style="color:var(--heading-color)">
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
                                class="form-control @error('title') is-invalid @enderror"
                                style="border-color:#e5e7eb;border-radius:10px"
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Starting Year --}}
                        <div class="col-12 col-sm-6">
                            <label for="year" class="form-label fw-semibold small" style="color:var(--heading-color)">
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
                                class="form-control @error('year') is-invalid @enderror"
                                style="border-color:#e5e7eb;border-radius:10px"
                            >
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- Info Note --}}
                    <div class="icp-note d-flex gap-3 p-3 mb-4 rounded-3">
                        @svg('heroicon-m-information-circle', 'icp-icon-sm flex-shrink-0 mt-1')
                        <p class="mb-0 small">
                            This admission year will be used as the active academic period
                            throughout the admission management system.
                        </p>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-5 py-2">
                            <span>Set active admission year</span>
                            @svg('heroicon-m-arrow-right', 'icp-icon-sm')
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection
