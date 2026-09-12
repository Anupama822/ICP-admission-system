@php($item = $item ?? null)

<div class="row g-4 mb-4">

    {{-- Admission Year Title --}}
    <div class="col-12 col-sm-6">
        <label for="title" class="form-label fw-semibold small icp-label">Admission year title</label>
        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $item?->title ?? date('Y')) }}"
            placeholder="e.g. 2026/27"
            required
            autofocus
            class="form-control icp-input @error('title') is-invalid @enderror"
        >
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Starting Year --}}
    <div class="col-12 col-sm-6">
        <label for="year" class="form-label fw-semibold small icp-label">Starting year</label>
        <select
            id="year"
            name="year"
            required
            class="form-select icp-input @error('year') is-invalid @enderror"
        >
            @foreach($years as $option)
                <option value="{{ $option }}" @selected((string) old('year', $item?->year ?? date('Y')) === (string) $option)>{{ $option }}</option>
            @endforeach
        </select>
        @error('year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Intake --}}
    <div class="col-12 col-sm-6">
        <label for="intake" class="form-label fw-semibold small icp-label">Intake</label>
        <select id="intake" name="intake" required class="form-select icp-input @error('intake') is-invalid @enderror">
            @foreach($intakes as $option)
                <option value="{{ $option }}" @selected(old('intake', $item?->intake) === $option)>{{ $option }}</option>
            @endforeach
        </select>
        @error('intake')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Active flag --}}
    <div class="col-12">
        <div class="form-check icp-form-check">
            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active"
                @checked(old('is_active', $item?->is_active ?? false))
                @disabled($item?->is_active)>
            <label class="form-check-label fw-semibold small icp-label" for="is_active">
                Make this the active admission year
            </label>
            <div class="form-text small">
                @if($item?->is_active)
                    This is already the active admission year.
                @else
                    Activating this year deactivates the current one.
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Info Note --}}
<div class="icp-note d-flex gap-3 p-3 mb-4 rounded-3">
    @svg('heroicon-m-information-circle', 'icp-icon-sm flex-shrink-0 mt-1')
    <p class="mb-0 small">
        The active admission year is used as the current academic period throughout
        the admission management system.
    </p>
</div>
