@extends('layouts.app')

@section('title', 'Create Admission Year')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-7">

        <div class="icp-form-card">

            {{-- Header --}}
            <div class="icp-form-header d-flex align-items-center gap-3">
                <div class="form-icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <div>
                    <h1 style="font-size:1.4rem;font-weight:800;color:var(--highlight-heading-color);margin:0;letter-spacing:-.3px">
                        Create Admission Year
                    </h1>
                    <p class="mb-0 small" style="color:var(--general-color);margin-top:.2rem">
                        Create the admission year that will be active for the system.
                    </p>
                </div>
            </div>

            {{-- Form --}}
            <div class="icp-form-body">
                <form method="POST" action="{{ route('admission-year.store') }}">
                    @csrf

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
                    <div class="d-flex gap-3 p-3 mb-4 rounded-3"
                        style="background:rgba(141,34,41,.05);border:1px solid rgba(141,34,41,.12)">
                        <i class="fas fa-info-circle mt-1 flex-shrink-0" style="color:var(--btn-color)"></i>
                        <p class="mb-0 small" style="color:var(--heading-color);line-height:1.6">
                            This admission year will be used as the active academic period
                            throughout the admission management system.
                        </p>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('admission-year.setup') }}"
                            class="btn px-4 py-2 fw-bold"
                            style="background:#f3f4f6;color:var(--general-color);border:none;border-radius:10px">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-5 py-2">
                            Create Admission Year
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection
