@php($item = $item ?? null)

<div class="row g-4 mb-4">

    {{-- Title --}}
    <div class="col-12 col-sm-8">
        <label for="title" class="form-label fw-semibold small icp-label">Course title</label>
        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $item?->title) }}"
            placeholder="e.g. Introduction to Programming"
            required
            autofocus
            class="form-control icp-input @error('title') is-invalid @enderror"
        >
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Credits --}}
    <div class="col-12 col-sm-4">
        <label for="credits" class="form-label fw-semibold small icp-label">Credits <span class="text-muted fw-normal">(optional)</span></label>
        <input
            id="credits"
            name="credits"
            type="number"
            step="0.5"
            min="0"
            max="20"
            value="{{ old('credits', $item?->credits) }}"
            placeholder="e.g. 3"
            class="form-control icp-input @error('credits') is-invalid @enderror"
        >
        @error('credits')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Description --}}
    <div class="col-12">
        <label for="description" class="form-label fw-semibold small icp-label">Description <span class="text-muted fw-normal">(optional)</span></label>
        <textarea
            id="description"
            name="description"
            rows="4"
            placeholder="A short summary of what this course covers"
            class="form-control icp-input @error('description') is-invalid @enderror"
        >{{ old('description', $item?->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>
