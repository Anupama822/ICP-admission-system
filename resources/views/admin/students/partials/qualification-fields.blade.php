@php
    $data = $data ?? [];
    $existingDocuments = collect($data['existing_documents'] ?? [])->map(function ($document) {
        return is_array($document)
            ? ['file_path' => $document['file_path'] ?? null, 'original_filename' => $document['original_filename'] ?? basename($document['file_path'] ?? '')]
            : ['file_path' => $document, 'original_filename' => basename((string) $document)];
    })->filter(fn ($document) => $document['file_path']);
@endphp

<div class="row g-3">
    <div class="col-12 col-sm-6 col-lg-3">
        <label class="form-label small icp-label">Educational Board</label>
        <select name="{{ $name }}[document_type]" required class="form-select icp-input js-document-type-select">
            <option value="" disabled @selected(empty($data['document_type']))>Select&hellip;</option>
            @foreach($documentTypes as $documentType)
                <option value="{{ $documentType->name }}" @selected(($data['document_type'] ?? null) === $documentType->name)>{{ $documentType->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-sm-3 col-lg-2">
        <label class="form-label small icp-label">Awarded year</label>
        <input type="number" name="{{ $name }}[awarded_year]" value="{{ $data['awarded_year'] ?? null }}" class="form-control icp-input">
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <label class="form-label small icp-label">Faculty</label>
        <input type="text" name="{{ $name }}[faculty]" value="{{ $data['faculty'] ?? null }}" list="faculty-options" placeholder="e.g. Management" required class="form-control icp-input">
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <label class="form-label small icp-label">Institute</label>
        <input type="text" name="{{ $name }}[institute_name]" value="{{ $data['institute_name'] ?? null }}" list="institute-options" required class="form-control icp-input">
    </div>
    <div class="col-6 col-sm-3 col-lg-2">
        <label class="form-label small icp-label">Score type</label>
        <select name="{{ $name }}[score_type]" class="form-select icp-input js-score-type">
            <option value="" @selected(empty($data['score_type']))>&mdash;</option>
            @foreach(\App\Models\StudentQualification::SCORE_TYPES as $scoreType)
                <option value="{{ $scoreType }}" @selected(($data['score_type'] ?? null) === $scoreType)>{{ $scoreType }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-sm-3 col-lg-2">
        <label class="form-label small icp-label">Score</label>
        <input type="text" name="{{ $name }}[score]" value="{{ $data['score'] ?? null }}"
            placeholder="{{ $scorePlaceholders[$data['score_type'] ?? null] ?? '' }}" class="form-control icp-input js-score-value">
    </div>

    <div class="col-12">
        <p class="fw-semibold small mb-2">Qualification Description</p>
        <div class="form-text small js-format-hint mb-2">{{ $documentTypeFormatHints[$data['document_type'] ?? null] ?? '' }}</div>
        <textarea name="{{ $name }}[qualification_description]" rows="2" placeholder="Describe the qualification&hellip;" class="form-control icp-input js-qualification-description mb-2">{{ $data['qualification_description'] ?? null }}</textarea>
        @if($existingDocuments->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 mb-2 js-existing-documents">
                @foreach($existingDocuments as $document)
                    <span class="icp-chip d-inline-flex align-items-center gap-1 px-2 py-1 border rounded-3 small">
                        <input type="hidden" name="{{ $name }}[existing_documents][]" value="{{ $document['file_path'] }}">
                        <a href="{{ Storage::url($document['file_path']) }}" target="_blank">{{ $document['original_filename'] }}</a>
                        <button type="button" class="btn-close js-remove-existing-document" style="font-size:.6rem" aria-label="Remove"></button>
                    </span>
                @endforeach
            </div>
        @endif
        <div class="form-text small text-muted mb-1">Attach images (optional, multiple allowed)</div>
        <input type="file" name="{{ $name }}[documents][]" accept="image/*" multiple class="form-control icp-input js-multi-file-input">
        <div class="d-flex flex-wrap gap-2 mt-2 js-new-documents-preview"></div>
    </div>
</div>
