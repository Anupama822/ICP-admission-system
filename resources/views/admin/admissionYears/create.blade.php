@extends('admin.templates.create')

@section('title', 'Create Admission Year')

@section('form_content')

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
@endsection
