@php
    $item = $item ?? null;

    // Prefer previously-submitted data (a validation failure elsewhere on
    // the form shouldn't wipe out what the admin already typed in) over
    // what's on file.
    $oldHighestQualification = old('highest_qualification');
    if (is_array($oldHighestQualification)) {
        $highestQualification = $oldHighestQualification;
    } else {
        $highestModel = $item?->qualifications->firstWhere('is_highest', true) ?? $item?->qualifications->first();
        $highestQualification = $highestModel ? [
            'document_type' => $highestModel->document_type,
            'institute_name' => $highestModel->institute_name,
        ] : [];
    }

    // Qualification descriptions are a repeatable list, each with its own
    // educational board, kept separate from the single required "highest
    // education" record above.
    $normalizeQualificationDescription = fn ($qualification) => [
        'document_type' => $qualification->document_type,
        'qualification_description' => $qualification->qualification_description,
        'existing_documents' => $qualification->documents->map(fn ($document) => [
            'file_path' => $document->file_path,
            'original_filename' => $document->original_filename,
        ])->all(),
    ];

    $oldQualifications = old('qualifications');
    if (is_array($oldQualifications)) {
        $qualifications = collect($oldQualifications)->values();
    } elseif ($item) {
        $qualifications = $item->qualifications->where('is_highest', false)->where('is_record', false)->values()->map($normalizeQualificationDescription);
    } else {
        $qualifications = collect();
    }

    // Academic Qualifications: another repeatable list, matching the format
    // of the old paper record (Document Type/Awarded Year/Faculty/Institute/
    // Score). Document Type is always "Academic" here.
    $normalizeAcademicRecord = fn ($qualification) => [
        'awarded_year' => $qualification->awarded_year,
        'faculty' => $qualification->faculty,
        'institute_name' => $qualification->institute_name,
        'score' => $qualification->score,
    ];

    $oldAcademicRecords = old('academic_records');
    if (is_array($oldAcademicRecords)) {
        $academicRecords = collect($oldAcademicRecords)->values();
    } elseif ($item) {
        $academicRecords = $item->qualifications->where('is_record', true)->values()->map($normalizeAcademicRecord);
    } else {
        $academicRecords = collect();
    }

    // Same idea for the "Add documents" rows: only their title (plain text)
    // survives a validation failure, since browsers never let a page refill
    // a file input, but that's still one less thing to retype.
    $oldDocumentRows = collect(old('documents', []))->values();

    $formSteps = [
        1 => 'Course & Intake',
        2 => 'Student Info',
        3 => 'Contact',
        4 => 'Parent / Guardian',
        5 => 'Academic',
        6 => 'Medical',
        7 => 'Documents',
        8 => 'Signature',
    ];
@endphp

<div class="d-flex flex-wrap gap-1 mb-4" id="form-stepper">
    @foreach($formSteps as $number => $label)
        <button type="button" class="btn js-step-nav d-flex flex-column align-items-center gap-1 flex-fill py-2 border-0 bg-transparent" data-step="{{ $number }}">
            <span class="js-step-circle rounded-circle d-inline-flex align-items-center justify-content-center fw-bold" style="width:26px;height:26px;font-size:.75rem;background:var(--background-color);color:var(--general-color);transition:background-color .15s ease,color .15s ease">{{ $number }}</span>
            <span class="js-step-label small text-center" style="line-height:1.1">{{ $label }}</span>
        </button>
    @endforeach
</div>

{{-- ── Course & Intake Information ── --}}
<div class="icp-form-section form-step" data-step="1">
    <div class="icp-card-header">
        <p class="icp-card-title">Course &amp; Intake Information</p>

        {{-- Info bar: Student ID / Group / Admission Year shown side by side --}}
        <div class="icp-info-bar d-flex flex-wrap gap-3 mb-4 justify-content-between align-items-center">
            @if($item)
                <div class="icp-info-chip">
                    <span class="icp-info-label">Student ID</span>
                    <span class="icp-info-value">{{ $item->admission_id }}</span>
                </div>
                <div class="icp-info-chip">
                    <span class="icp-info-label">Group</span>
                    <span class="icp-info-value">{{ $item->group }}</span>
                    <span class="icp-info-badge">auto-assigned</span>
                </div>
            @else
                <div class="icp-info-chip icp-info-chip-muted">
                    <span class="icp-info-value">Student ID &amp; group are assigned automatically on submit.</span>
                </div>
            @endif

            <div class="d-flex justify-content-end ">
            @if($selectedAdmissionYear)
                <div class="icp-info-chip d-flex justify-content-end align-items-center text-muted icp-note gap-2 p-2 rounded-3 mb-0">
                    @svg('heroicon-m-calendar-days', 'icp-icon-sm flex-shrink-0')
                    <div>
                        <span class="icp-info-label">{{ $item ? 'Admission year' : 'Enrolling under' }}</span>
                        <span class="icp-info-value">{{ $selectedAdmissionYear->title }} &middot; {{ $selectedAdmissionYear->intake }} intake</span>
                    </div>
                </div>
                <input type="hidden" name="admission_year_id" value="{{ $selectedAdmissionYearId }}">
            @else
                <div class="icp-info-chip icp-info-chip-danger">
                    @svg('heroicon-m-exclamation-triangle', 'icp-icon-sm flex-shrink-0')
                    <span class="icp-info-value">No active admission year. <a href="{{ route('admission-year.index') }}">Activate one</a> to continue.</span>
                </div>
            @endif
            </div>
        </div>
        @error('admission_year_id') <div class="text-danger small mb-3">{{ $message }}</div> @enderror
    </div>

    <div class="icp-form-section-body">
        <div class="row g-4">
            <div class="col-12 col-sm-6">
                <label for="course_id" class="form-label fw-semibold small icp-label">Course</label>
                <select id="course_id" name="course_id" required class="form-select icp-input @error('course_id') is-invalid @enderror">
                    <option value="" disabled @selected(! $selectedCourseId)>Select a course&hellip;</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" data-levels='@json($course->levels ?? [])' @selected($selectedCourseId === $course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
                @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6">
                <label for="level" class="form-label fw-semibold small icp-label">Level</label>
                <select id="level" name="level" required class="form-select icp-input @error('level') is-invalid @enderror">
                    <option value="" disabled @selected(! $selectedLevel)>{{ $selectedCourse ? 'Select a level…' : 'Select a course first…' }}</option>
                    @foreach($selectedCourse?->levels ?? [] as $levelOption)
                        <option value="{{ $levelOption }}" @selected($selectedLevel === $levelOption)>{{ $levelOption }}</option>
                    @endforeach
                </select>
                @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6">
                <label for="entry_type" class="form-label fw-semibold small icp-label">Entry type</label>
                <select id="entry_type" name="entry_type" required class="form-select icp-input @error('entry_type') is-invalid @enderror">
                    <option value="" disabled @selected(! old('entry_type', $item?->entry_type))>Select entry type&hellip;</option>
                    @foreach($entryTypeOptions as $option)
                        <option value="{{ $option }}" @selected(old('entry_type', $item?->entry_type) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                @error('entry_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- ── Student Information ── --}}
<div class="icp-form-section form-step" data-step="2">
    <div class="icp-card-header">
        <p class="icp-card-title">Student Information</p>
    </div>
    <div class="icp-form-section-body">
        <div class="row g-4">
            <div class="col-12">
                <label class="form-label fw-semibold small icp-label">Photo <span class="text-muted fw-normal">(JPG, optional)</span></label>
                <div class="d-flex align-items-center gap-3">
                    <div class="icp-photo-picker position-relative flex-shrink-0">
                        <img id="photo-preview" src="{{ $item?->photo_path ? Storage::url($item->photo_path) : '' }}"
                            alt="Student photo" class="icp-photo-preview" style="{{ $item?->photo_path ? '' : 'display:none' }}">
                        <div id="photo-placeholder" class="icp-photo-placeholder" style="{{ $item?->photo_path ? 'display:none' : '' }}">
                            @svg('heroicon-m-user')
                        </div>
                        <label for="photo" class="icp-photo-edit-btn" title="{{ $item?->photo_path ? 'Replace photo' : 'Upload photo' }}">
                            @svg('heroicon-m-camera')
                        </label>
                    </div>
                    <div>
                        <input id="photo" name="photo" type="file" accept="image/jpeg" class="d-none @error('photo') is-invalid @enderror">
                        @error('photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        
            <div class="col-12 col-sm-4">
                <label for="first_name" class="form-label fw-semibold small icp-label">First name</label>
                <input id="first_name" name="first_name" type="text" required
                    value="{{ old('first_name', $item?->first_name) }}"
                    class="form-control icp-input @error('first_name') is-invalid @enderror">
                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="middle_name" class="form-label fw-semibold small icp-label">Middle name <span class="text-muted fw-normal">(optional)</span></label>
                <input id="middle_name" name="middle_name" type="text"
                    value="{{ old('middle_name', $item?->middle_name) }}"
                    class="form-control icp-input @error('middle_name') is-invalid @enderror">
                @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="last_name" class="form-label fw-semibold small icp-label">Last name</label>
                <input id="last_name" name="last_name" type="text" required
                    value="{{ old('last_name', $item?->last_name) }}"
                    class="form-control icp-input @error('last_name') is-invalid @enderror">
                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            
            <div class="col-12 col-sm-6">
                <label for="gender" class="form-label fw-semibold small icp-label">Gender</label>
                <select id="gender" name="gender" required class="form-select icp-input @error('gender') is-invalid @enderror">
                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('gender', $item?->gender) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6">
                <label for="dob_bs" class="form-label fw-semibold small icp-label">Date of birth (BS) <span class="text-muted fw-normal">(optional)</span></label>
                <input id="dob_bs" name="dob_bs" type="text" placeholder="e.g. 2062-03-05"
                    value="{{ old('dob_bs', $item?->dob_bs) }}"
                    class="form-control icp-input @error('dob_bs') is-invalid @enderror">
                @error('dob_bs') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6">
                <label for="dob_ad" class="form-label fw-semibold small icp-label">Date of birth (AD)</label>
                <input id="dob_ad" name="dob_ad" type="date" required
                    value="{{ old('dob_ad', $item?->dob_ad?->format('Y-m-d')) }}"
                    class="form-control icp-input @error('dob_ad') is-invalid @enderror">
                @error('dob_ad') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <div class="icp-note d-flex gap-3 p-3 rounded-3 mb-0">
                    @svg('heroicon-m-information-circle', 'icp-icon-sm flex-shrink-0 mt-1')
                    <p class="mb-0 small">Provide a citizenship number or a passport number below &mdash; at least one is required.</p>
                </div>
            </div>

            <div class="col-12 col-sm-6">
                <label for="citizenship_number" class="form-label fw-semibold small icp-label">Citizenship number</label>
                <input id="citizenship_number" name="citizenship_number" type="text"
                    placeholder="e.g. 12-34-56-78901"
                    pattern="\d{2}-\d{2}-\d{2}-\d{5}"
                    title="Format: 12-34-56-78901"
                    value="{{ old('citizenship_number', $item?->citizenship_number) }}"
                    class="form-control icp-input @error('citizenship_number') is-invalid @enderror">
                @error('citizenship_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6">
                <label for="citizenship_issued_date" class="form-label fw-semibold small icp-label">Citizenship issued date (BS)</label>
                <input id="citizenship_issued_date" name="citizenship_issued_date" type="text" placeholder="e.g. 2062-03-05"
                    value="{{ old('citizenship_issued_date', $item?->citizenship_issued_date) }}"
                    class="form-control icp-input @error('citizenship_issued_date') is-invalid @enderror">
                @error('citizenship_issued_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6">
                <label for="passport_number" class="form-label fw-semibold small icp-label">Passport number</label>
                <input id="passport_number" name="passport_number" type="text"
                    placeholder="e.g. PA1234567"
                    pattern="[A-Za-z]{2}[0-9]{7}"
                    title="Format: PA1234567"
                    value="{{ old('passport_number', $item?->passport_number) }}"
                    class="form-control icp-input @error('passport_number') is-invalid @enderror">
                @error('passport_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6">
                <label for="passport_issued_date" class="form-label fw-semibold small icp-label">Passport issued date (BS)</label>
                <input id="passport_issued_date" name="passport_issued_date" type="text" placeholder="e.g. 2062-03-05"
                    value="{{ old('passport_issued_date', $item?->passport_issued_date) }}"
                    class="form-control icp-input @error('passport_issued_date') is-invalid @enderror">
                @error('passport_issued_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            
            
        </div>
    </div>
</div>

{{-- ── Contact Information ── --}}
<div class="icp-form-section form-step" data-step="3`">
    <div class="icp-card-header">
        <p class="icp-card-title">Contact Information</p>
    </div>
    <div class="icp-form-section-body">
        <div class="row g-4">
            <div class="col-12 col-sm-6">
                <label for="permanent_address" class="form-label fw-semibold small icp-label">Permanent address</label>
                <input id="permanent_address" name="permanent_address" type="text" required
                    value="{{ old('permanent_address', $item?->permanent_address) }}"
                    class="form-control icp-input @error('permanent_address') is-invalid @enderror">
                @error('permanent_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6">
                <label for="corresponding_address" class="form-label fw-semibold small icp-label">Corresponding address <span class="text-muted fw-normal">(optional)</span></label>
                <input id="corresponding_address" name="corresponding_address" type="text" 
                    value="{{ old('corresponding_address', $item?->corresponding_address) }}"
                    class="form-control icp-input @error('corresponding_address') is-invalid @enderror">
                @error('corresponding_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-4">
                <label for="mobile" class="form-label fw-semibold small icp-label">Mobile <span class="text-muted fw-normal">(optional)</span></label>
                <input id="mobile" name="mobile" type="text" 
                    value="{{ old('mobile', $item?->mobile) }}"
                    class="form-control icp-input @error('mobile') is-invalid @enderror">
                @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="email_1" class="form-label fw-semibold small icp-label">Email address <span class="text-muted fw-normal">(optional)</span></label>
                <input id="email_1" name="email_1" type="email" 
                    value="{{ old('email_1', $item?->email_1) }}"
                    class="form-control icp-input @error('email_1') is-invalid @enderror">
                @error('email_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="email_2" class="form-label fw-semibold small icp-label">Secondary email <span class="text-muted fw-normal">(optional)</span></label>
                <input id="email_2" name="email_2" type="email"
                    value="{{ old('email_2', $item?->email_2) }}"
                    class="form-control icp-input @error('email_2') is-invalid @enderror">
                @error('email_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- ── Parent / Guardian Information ── --}}
<div class="icp-form-section form-step" data-step="4">
    <div class="icp-card-header">
        <p class="icp-card-title">Parent / Guardian Information</p>
    </div>
    <div class="icp-form-section-body">
        <div class="row g-4">
            <div class="col-12">
                <div class="icp-note d-flex gap-3 p-3 rounded-3 mb-0">
                    @svg('heroicon-m-information-circle', 'icp-icon-sm flex-shrink-0 mt-1')
                    <p class="mb-0 small">Provide at least one parent or guardian's information below.</p>
                </div>
                @error('father_full_name') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-4">
                <label for="father_full_name" class="form-label fw-semibold small icp-label">Father's full name</label>
                <input id="father_full_name" name="father_full_name" type="text"
                    value="{{ old('father_full_name', $item?->father_full_name) }}"
                    class="form-control icp-input @error('father_full_name') is-invalid @enderror">
            </div>
            <div class="col-12 col-sm-4">
                <label for="father_mobile" class="form-label fw-semibold small icp-label">Father's mobile</label>
                <input id="father_mobile" name="father_mobile" type="text"
                    value="{{ old('father_mobile', $item?->father_mobile) }}"
                    class="form-control icp-input @error('father_mobile') is-invalid @enderror">
                @error('father_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="father_email" class="form-label fw-semibold small icp-label">Father's email </label>
                <input id="father_email" name="father_email" type="email"
                    value="{{ old('father_email', $item?->father_email) }}"
                    class="form-control icp-input @error('father_email') is-invalid @enderror">
                @error('father_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-4">
                <label for="mother_full_name" class="form-label fw-semibold small icp-label">Mother's full name </label>
                <input id="mother_full_name" name="mother_full_name" type="text"
                    value="{{ old('mother_full_name', $item?->mother_full_name) }}"
                    class="form-control icp-input @error('mother_full_name') is-invalid @enderror">
                @error('mother_full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="mother_mobile" class="form-label fw-semibold small icp-label">Mother's mobile </label>
                <input id="mother_mobile" name="mother_mobile" type="text"
                    value="{{ old('mother_mobile', $item?->mother_mobile) }}"
                    class="form-control icp-input @error('mother_mobile') is-invalid @enderror">
                @error('mother_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="mother_email" class="form-label fw-semibold small icp-label">Mother's email </label>
                <input id="mother_email" name="mother_email" type="email"
                    value="{{ old('mother_email', $item?->mother_email) }}"
                    class="form-control icp-input @error('mother_email') is-invalid @enderror">
                @error('mother_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <label for="guardian_full_name" class="form-label fw-semibold small icp-label">Local guardian's full name </label>
                <input id="guardian_full_name" name="guardian_full_name" type="text"
                    value="{{ old('guardian_full_name', $item?->guardian_full_name) }}"
                    class="form-control icp-input @error('guardian_full_name') is-invalid @enderror">
                @error('guardian_full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="guardian_relationship" class="form-label fw-semibold small icp-label">Relationship to student </label>
                <input id="guardian_relationship" name="guardian_relationship" type="text" placeholder="e.g. Uncle"
                    value="{{ old('guardian_relationship', $item?->guardian_relationship) }}"
                    class="form-control icp-input @error('guardian_relationship') is-invalid @enderror">
                @error('guardian_relationship') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="guardian_contact" class="form-label fw-semibold small icp-label">Local guardian's contact </label>
                <input id="guardian_contact" name="guardian_contact" type="text"
                    value="{{ old('guardian_contact', $item?->guardian_contact) }}"
                    class="form-control icp-input @error('guardian_contact') is-invalid @enderror">
                @error('guardian_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="guardian_email" class="form-label fw-semibold small icp-label">Local guardian's email </label>
                <input id="guardian_email" name="guardian_email" type="email"
                    value="{{ old('guardian_email', $item?->guardian_email) }}"
                    class="form-control icp-input @error('guardian_email') is-invalid @enderror">
                @error('guardian_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- ── Academic / Education Information ── --}}
<div class="icp-form-section form-step" data-step="5">
    <div class="icp-card-header">
        <p class="icp-card-title">Academic / Education Information</p>
        <p class="icp-card-subtitle">The highest qualification the student holds.</p>
    </div>
    <div class="icp-form-section-body">
        @if($documentTypes->isEmpty())
            <div class="icp-note d-flex gap-3 p-3 rounded-3 mb-3">
                @svg('heroicon-m-exclamation-triangle', 'icp-icon-sm flex-shrink-0 mt-1')
                <p class="mb-0 small">No educational boards have been set up yet. <a href="{{ route('admin.document-types.index') }}" target="_blank">Add one</a> before this can be saved.</p>
            </div>
        @endif

        <p class="fw-semibold small mb-2">Highest Education</p>
        <div class="qualification-row border rounded-3 p-3">
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <label class="form-label small icp-label">Educational Board</label>
                    <select name="highest_qualification[document_type]" required class="form-select icp-input js-document-type-select">
                        <option value="" disabled @selected(empty($highestQualification['document_type']))>Select&hellip;</option>
                        @foreach($documentTypes as $documentType)
                            <option value="{{ $documentType->name }}" @selected(($highestQualification['document_type'] ?? null) === $documentType->name)>{{ $documentType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label small icp-label">Awarding Body</label>
                    <select name="highest_qualification[institute_name]" required class="form-select icp-input js-creatable-select" data-group="institute" data-store-url="{{ route('admin.institutes.store') }}">
                        <option value="" disabled @selected(empty($highestQualification['institute_name']))>Select&hellip;</option>
                        @foreach($institutes as $institute)
                            <option value="{{ $institute->name }}" @selected(($highestQualification['institute_name'] ?? null) === $institute->name)>{{ $institute->name }}</option>
                        @endforeach
                        <option value="__add_new__">+ Add new institute&hellip;</option>
                    </select>
                </div>
            </div>
        </div>

        <p class="fw-semibold small mb-2 mt-4">Qualification Description</p>
        <div id="qualification-descriptions-container" class="d-flex flex-column gap-3 mb-3">
            @foreach($qualifications as $qualification)
                @php
                    $qualificationExistingDocuments = collect($qualification['existing_documents'] ?? [])->map(function ($document) {
                        return is_array($document)
                            ? ['file_path' => $document['file_path'] ?? null, 'original_filename' => $document['original_filename'] ?? basename($document['file_path'] ?? '')]
                            : ['file_path' => $document, 'original_filename' => basename((string) $document)];
                    })->filter(fn ($document) => $document['file_path']);
                @endphp
                <div class="qualification-row border rounded-3 p-3 position-relative">
                    <button type="button" class="btn-close js-remove-row position-absolute top-0 end-0 m-2" aria-label="Remove"></button>
                    <div class="col-12 col-sm-4 mb-3">
                        <label class="form-label small icp-label">Educational Board</label>
                        <select name="qualifications[{{ $loop->index }}][document_type]" required class="form-select icp-input js-document-type-select">
                            <option value="" disabled @selected(empty($qualification['document_type']))>Select&hellip;</option>
                            @foreach($documentTypes as $documentType)
                                <option value="{{ $documentType->name }}" @selected(($qualification['document_type'] ?? null) === $documentType->name)>{{ $documentType->name }}, {{ $documentType->format_hint }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-text small js-format-hint mb-2">{{ $documentTypeFormatHints[$qualification['document_type'] ?? null] ?? '' }}</div>
                    <textarea name="qualifications[{{ $loop->index }}][qualification_description]" rows="2" placeholder="Describe the qualification&hellip;" class="form-control icp-input js-qualification-description mb-2">{{ $qualification['qualification_description'] ?? null }}</textarea>
                    @if($qualificationExistingDocuments->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 mb-2 js-existing-documents">
                            @foreach($qualificationExistingDocuments as $document)
                                <span class="icp-chip d-inline-flex align-items-center gap-1 px-2 py-1 border rounded-3 small">
                                    <input type="hidden" name="qualifications[{{ $loop->parent->index }}][existing_documents][]" value="{{ $document['file_path'] }}">
                                    <a href="{{ Storage::url($document['file_path']) }}" target="_blank">{{ $document['original_filename'] }}</a>
                                    <button type="button" class="btn-close js-remove-existing-document" style="font-size:.6rem" aria-label="Remove"></button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                    <div class="form-text small text-muted mb-1">Attach images (optional, multiple allowed)</div>
                    <input type="file" name="qualifications[{{ $loop->index }}][documents][]" accept="image/*" multiple class="form-control icp-input js-multi-file-input">
                    <div class="d-flex flex-wrap gap-2 mt-2 js-new-documents-preview"></div>
                </div>
            @endforeach
        </div>
        <button type="button" id="add-qualification-description" class="btn icp-btn-muted d-inline-flex align-items-center gap-2 px-3 py-2">
            @svg('heroicon-m-plus', 'icp-icon-sm')
            <span>Add Qualification Description</span>
        </button>

        <template id="qualification-description-template">
            <div class="qualification-row border rounded-3 p-3 position-relative">
                <button type="button" class="btn-close js-remove-row position-absolute top-0 end-0 m-2" aria-label="Remove"></button>
                <div class="col-12 col-sm-4 mb-3">
                    <label class="form-label small icp-label">Educational Board</label>
                    <select name="qualifications[__INDEX__][document_type]" required class="form-select icp-input js-document-type-select">
                        <option value="" disabled selected>Select&hellip;</option>
                        @foreach($documentTypes as $documentType)
                            <option value="{{ $documentType->name }}">{{ $documentType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-text small js-format-hint mb-2"></div>
                <textarea name="qualifications[__INDEX__][qualification_description]" rows="2" placeholder="Describe the qualification&hellip;" class="form-control icp-input js-qualification-description mb-2"></textarea>
                <div class="form-text small text-muted mb-1">Attach images (optional, multiple allowed)</div>
                <input type="file" name="qualifications[__INDEX__][documents][]" accept="image/*" multiple class="form-control icp-input js-multi-file-input">
                <div class="d-flex flex-wrap gap-2 mt-2 js-new-documents-preview"></div>
            </div>
        </template>

        <p class="fw-semibold small mb-2 mt-4">Academic Qualifications</p>
        <div id="academic-records-container" class="d-flex flex-column gap-3 mb-3">
            @foreach($academicRecords as $record)
                <div class="qualification-row border rounded-3 p-3 position-relative">
                    <button type="button" class="btn-close js-remove-row position-absolute top-0 end-0 m-2" aria-label="Remove"></button>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label small icp-label">Document Type</label>
                            <input type="text" name="academic_records[{{ $loop->index }}][document_type]" value="Academic" readonly class="form-control icp-input">
                        </div>
                        <div class="col-6 col-sm-3 col-lg-2">
                            <label class="form-label small icp-label">Awarded year</label>
                            <input type="number" name="academic_records[{{ $loop->index }}][awarded_year]" value="{{ $record['awarded_year'] ?? null }}" class="form-control icp-input">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label small icp-label">Faculty</label>
                            <select name="academic_records[{{ $loop->index }}][faculty]" required class="form-select icp-input js-creatable-select" data-group="faculty" data-store-url="{{ route('admin.faculties.store') }}">
                                <option value="" disabled @selected(empty($record['faculty']))>Select&hellip;</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->name }}" @selected(($record['faculty'] ?? null) === $faculty->name)>{{ $faculty->name }}</option>
                                @endforeach
                                <option value="__add_new__">+ Add new faculty&hellip;</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label small icp-label">Institute Name</label>
                            <select name="academic_records[{{ $loop->index }}][institute_name]" required class="form-select icp-input js-creatable-select" data-group="institute" data-store-url="{{ route('admin.institutes.store') }}">
                                <option value="" disabled @selected(empty($record['institute_name']))>Select&hellip;</option>
                                @foreach($institutes as $institute)
                                    <option value="{{ $institute->name }}" @selected(($record['institute_name'] ?? null) === $institute->name)>{{ $institute->name }}</option>
                                @endforeach
                                <option value="__add_new__">+ Add new institute&hellip;</option>
                            </select>
                        </div>
                        <div class="col-6 col-sm-3 col-lg-2">
                            <label class="form-label small icp-label">Score</label>
                            <input type="text" name="academic_records[{{ $loop->index }}][score]" value="{{ $record['score'] ?? null }}" placeholder="e.g. GPA-3.35" class="form-control icp-input">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" id="add-academic-record" class="btn icp-btn-muted d-inline-flex align-items-center gap-2 px-3 py-2">
            @svg('heroicon-m-plus', 'icp-icon-sm')
            <span>Add Academic Qualification</span>
        </button>

        <template id="academic-record-template">
            <div class="qualification-row border rounded-3 p-3 position-relative">
                <button type="button" class="btn-close js-remove-row position-absolute top-0 end-0 m-2" aria-label="Remove"></button>
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small icp-label">Document Type</label>
                        <input type="text" name="academic_records[__INDEX__][document_type]" value="Academic" readonly class="form-control icp-input">
                    </div>
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="form-label small icp-label">Awarded year</label>
                        <input type="number" name="academic_records[__INDEX__][awarded_year]" class="form-control icp-input">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small icp-label">Faculty</label>
                        <select name="academic_records[__INDEX__][faculty]" required class="form-select icp-input js-creatable-select" data-group="faculty" data-store-url="{{ route('admin.faculties.store') }}">
                            <option value="" disabled selected>Select&hellip;</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->name }}">{{ $faculty->name }}</option>
                            @endforeach
                            <option value="__add_new__">+ Add new faculty&hellip;</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label small icp-label">Institute Name</label>
                        <select name="academic_records[__INDEX__][institute_name]" required class="form-select icp-input js-creatable-select" data-group="institute" data-store-url="{{ route('admin.institutes.store') }}">
                            <option value="" disabled selected>Select&hellip;</option>
                            @foreach($institutes as $institute)
                                <option value="{{ $institute->name }}">{{ $institute->name }}</option>
                            @endforeach
                            <option value="__add_new__">+ Add new institute&hellip;</option>
                        </select>
                    </div>
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="form-label small icp-label">Score</label>
                        <input type="text" name="academic_records[__INDEX__][score]" placeholder="e.g. GPA-3.35" class="form-control icp-input">
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>


{{-- ── Medical Background ── --}}
<div class="icp-form-section form-step" data-step="6">
    <div class="icp-card-header">
        <p class="icp-card-title">Medical Background</p>
    </div>
    <div class="icp-form-section-body">
        <div class="row g-4">
            @foreach([
                'has_disorder' => 'Do you have a mental or physical disorder that poses or is likely to pose a threat to the safety or welfare of yourself or others?',
                'is_drug_abuser' => 'Are you, or have you ever been, a drug abuser or addict?',
                'has_criminal_record' => 'Have you ever been arrested or convicted for any offense or crime, even though subject of a pardon, amnesty, or other similar action?',
                'has_communicable_disease' => 'Do you have a communicable disease of public health significance, such as tuberculosis (TB)?',
                'is_minor_requiring_consent' => 'Is the student under 18 and considered underage, requiring consent from the student and parent/guardian for enrollment?',
            ] as $field => $question)
                <div class="col-12">
                    <label for="{{ $field }}" class="form-label fw-semibold small icp-label">{{ $question }}</label>
                    <select id="{{ $field }}" name="{{ $field }}" required class="form-select icp-input @error($field) is-invalid @enderror" style="max-width:160px">
                        <option value="1" @selected(old($field, $item?->{$field}) == 1)>Yes</option>
                        <option value="0" @selected(old($field, $item?->{$field} ?? '0') == 0)>No</option>
                    </select>
                    @error($field) <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Documents ── --}}
<div class="icp-form-section form-step" data-step="7">
    <div class="icp-card-header">
        <p class="icp-card-title">Documents</p>
        <p class="icp-card-subtitle">Attach scanned documents such as citizenship/passport. Academic transcripts are attached against each qualification in the Academic step.</p>
    </div>
    <div class="icp-form-section-body">
        @if($item && $item->documents->isNotEmpty())
            <p class="fw-semibold small mb-2">Already on file</p>
            <ul class="list-unstyled mb-3 d-flex flex-column gap-2">
                @foreach($item->documents as $document)
                    <li class="d-flex align-items-center justify-content-between border rounded-3 px-3 py-2">
                        <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="d-flex align-items-center gap-2 text-decoration-none">
                            @svg('heroicon-m-paper-clip', 'icp-icon-sm')
                            <span>{{ $document->title }} &mdash; {{ $document->original_filename }}</span>
                        </a>
                        <button type="submit" form="delete-document-{{ $document->id }}" class="btn btn-sm text-danger" title="Remove">
                            @svg('heroicon-m-trash', 'icp-icon-sm')
                        </button>
                    </li>
                    <form id="delete-document-{{ $document->id }}" method="POST" action="{{ route('admin.students.documents.destroy', [$item->getKey(), $document->getKey()]) }}">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            </ul>
        @endif

        <p class="fw-semibold small mb-2">Add documents</p>
        <div id="documents-container" class="d-flex flex-column gap-3 mb-3">
            @foreach($oldDocumentRows as $documentRow)
                <div class="document-row border rounded-3 p-3 position-relative">
                    <button type="button" class="btn-close js-remove-row position-absolute top-0 end-0 m-2" aria-label="Remove"></button>
                    <div class="row g-3">
                        <div class="col-12 col-sm-4">
                            <label class="form-label small icp-label">Title</label>
                            <input type="text" name="documents[{{ $loop->index }}][title]" value="{{ $documentRow['title'] ?? '' }}" required class="form-control icp-input js-document-title">
                        </div>
                        <div class="col-12 col-sm-8">
                            <label class="form-label small icp-label">Files <span class="text-muted fw-normal">(optional, multiple allowed)</span></label>
                            <input type="file" name="documents[{{ $loop->index }}][files][]" multiple class="form-control icp-input js-multi-file-input">
                            <div class="d-flex flex-wrap gap-2 mt-2 js-new-documents-preview"></div>
                            <div class="form-text small">Files aren&rsquo;t kept after a form error &mdash; please re-select them.</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex flex-wrap gap-2 mb-1">
            <button type="button" class="btn icp-btn-muted d-inline-flex align-items-center gap-2 px-3 py-2 js-add-document" data-title="Citizenship">
                @svg('heroicon-m-plus', 'icp-icon-sm')<span>Citizenship</span>
            </button>
            <button type="button" class="btn icp-btn-muted d-inline-flex align-items-center gap-2 px-3 py-2 js-add-document" data-title="Other Documents">
                @svg('heroicon-m-plus', 'icp-icon-sm')<span>Other Documents</span>
            </button>
        </div>

        <template id="document-row-template">
            <div class="document-row border rounded-3 p-3 position-relative">
                <button type="button" class="btn-close js-remove-row position-absolute top-0 end-0 m-2" aria-label="Remove"></button>
                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <label class="form-label small icp-label">Title</label>
                        <input type="text" name="documents[__INDEX__][title]" required class="form-control icp-input js-document-title">
                    </div>
                    <div class="col-12 col-sm-8">
                        <label class="form-label small icp-label">Files <span class="text-muted fw-normal">(optional, multiple allowed)</span></label>
                        <input type="file" name="documents[__INDEX__][files][]" multiple class="form-control icp-input js-multi-file-input">
                        <div class="d-flex flex-wrap gap-2 mt-2 js-new-documents-preview"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

{{-- ── Digital Signature ── --}}
<div class="icp-form-section form-step" data-step="8">
    <div class="icp-card-header">
        <p class="icp-card-title">Digital Signature</p>
        <p class="icp-card-subtitle">Captured from the signotec signature pad connected to this computer.</p>
    </div>
    <div class="icp-form-section-body">
        @if($item?->signature_path)
            <p class="small mb-2">Current signature on file:</p>
            <img src="{{ Storage::url($item->signature_path) }}" alt="Current signature" style="height:60px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:4px" class="mb-3 d-block">
            <p class="small text-muted mb-2">Capture a new one below only if you need to replace it.</p>
        @endif

        <div id="signotec-status" class="icp-note d-flex gap-3 p-3 rounded-3 mb-3">
            @svg('heroicon-m-information-circle', 'icp-icon-sm flex-shrink-0 mt-1')
            <p class="mb-0 small" id="signotec-status-text">Press "Capture Signature" to connect to the signotec pad.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <button type="button" id="js-signotec-start" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                @svg('heroicon-m-pencil', 'icp-icon-sm')
                <span>Capture Signature</span>
            </button>
            <button type="button" id="js-signotec-done" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2" style="display:none">
                @svg('heroicon-m-check', 'icp-icon-sm')
                <span>Done</span>
            </button>
            <button type="button" id="js-signotec-retry" class="btn icp-btn-muted px-4 py-2" style="display:none">Retry</button>
        </div>

        <p class="small mb-2" id="signotec-preview-label" style="display:none">Captured signature:</p>
        <img id="signotec-preview" alt="Captured signature" style="display:none;height:70px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:4px" class="mb-2 d-block">

        <input type="hidden" name="signature" id="signature-input">
        @error('signature') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4" id="form-stepper-nav">
    <button type="button" id="js-step-prev" class="btn icp-btn-muted px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" style="display:none">
        @svg('heroicon-m-arrow-left', 'icp-icon-sm')
        <span>Previous</span>
    </button>
    <button type="button" id="js-step-next" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 ms-auto">
        <span>Next</span>
        @svg('heroicon-m-arrow-right', 'icp-icon-sm')
    </button>
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="{{ asset('js/signotec/STPadServerLib.js') }}"></script>
    <script>
        (function ($) {
            'use strict';

            // ── Repeatable rows (qualifications / documents) ────────────────
            function makeRepeater(containerId, templateId, addButtonSelector, startIndex, onAdd) {
                var container = document.getElementById(containerId);
                var template = document.getElementById(templateId);
                var nextIndex = startIndex;

                function addRow(presetTitle) {
                    var fragment = template.content.cloneNode(true);
                    fragment.querySelectorAll('[name]').forEach(function (el) {
                        el.name = el.name.replace('__INDEX__', nextIndex);
                    });
                    if (presetTitle) {
                        var titleInput = fragment.querySelector('.js-document-title');
                        if (titleInput) { titleInput.value = presetTitle; }
                    }

                    // Captured before appendChild empties the fragment; the
                    // element references stay valid once moved into the DOM.
                    var newSelects = Array.prototype.slice.call(
                        fragment.querySelectorAll('.js-document-type-select, .js-creatable-select')
                    );

                    container.appendChild(fragment);
                    nextIndex++;

                    newSelects.forEach(function (select) {
                        if (window.icpPopulateCreatableOptions) { window.icpPopulateCreatableOptions(select); }
                        if (window.icpInitSelect2) { window.icpInitSelect2(select); }
                    });
                }

                $(document).on('click', addButtonSelector, function () {
                    addRow($(this).data('title'));
                });

                container.addEventListener('click', function (e) {
                    var button = e.target.closest('.js-remove-row');
                    if (button) {
                        button.closest('.qualification-row, .document-row').remove();
                    }
                });

                if (onAdd) { onAdd(addRow); }
            }

            makeRepeater('documents-container', 'document-row-template', '.js-add-document', {{ $oldDocumentRows->count() }});
            makeRepeater('qualification-descriptions-container', 'qualification-description-template', '#add-qualification-description', {{ $qualifications->count() }});
            makeRepeater('academic-records-container', 'academic-record-template', '#add-academic-record', {{ $academicRecords->count() }});

            // ── Highest qualification: drop a previously-uploaded document ─────
            document.addEventListener('click', function (e) {
                var button = e.target.closest('.js-remove-existing-document');
                if (button) {
                    button.closest('.icp-chip').remove();
                }
            });

            // ── Highest qualification: show the admin-set description format
            //    hint for the chosen Educational Board ──────────────────────────
            //    Delegated via jQuery (not document.addEventListener) because
            //    Select2 reports a selection by triggering a jQuery-only
            //    "change" event on the underlying <select> — it never fires
            //    a native DOM change event, so a plain addEventListener
            //    listener would never see it. ─────────────────────────────
            (function () {
                var formatHints = @json($documentTypeFormatHints);

                $(document).on('change', '.js-document-type-select', function () {
                    var hint = this.closest('.qualification-row').querySelector('.js-format-hint');
                    if (hint) {
                        hint.textContent = formatHints[this.value] || '';
                    }
                });
            })();

            // ── Searchable / creatable dropdowns (Educational Board, Faculty,
            //    Institute), via Select2. `.js-document-type-select` is
            //    search-only; `.js-creatable-select` (data-group +
            //    data-store-url) also offers two ways to add a new one: type
            //    a value that doesn't match anything (Select2's own "create"
            //    suggestion), or pick the always-visible "+ Add new…" entry
            //    at the bottom of the list. Either way the result is shared
            //    with every other select of the same group — e.g. adding an
            //    institute from "Awarding Body" also offers it under
            //    "Institute Name" and vice versa — and new rows added later
            //    (cloned from a <template>) start with the same up-to-date
            //    list too. ────────────────────────────────────────────────
            (function () {
                var csrf = $('meta[name="csrf-token"]').attr('content');
                var ADD_NEW = '__add_new__';

                // Kept in sync as new faculties/institutes are created, so a
                // qualification row added after the fact still offers them.
                var creatableOptions = {
                    faculty: @json($faculties->pluck('name')),
                    institute: @json($institutes->pluck('name'))
                };

                // Every creatable select's last option is its "+ Add new…"
                // entry; new real options are inserted just before it so it
                // always stays at the bottom of the list.
                function insertOption(select, name) {
                    if (select.querySelector('option[value="' + CSS.escape(name) + '"]')) { return; }
                    select.insertBefore(new Option(name, name, false, false), select.lastElementChild);
                }

                function addOptionToGroup(group, name) {
                    if (creatableOptions[group].indexOf(name) === -1) {
                        creatableOptions[group].push(name);
                    }

                    document.querySelectorAll('.js-creatable-select[data-group="' + group + '"]').forEach(function (select) {
                        insertOption(select, name);
                        $(select).trigger('change.select2');
                    });
                }

                function persistNewOption(select, name) {
                    $.ajax({
                        url: select.dataset.storeUrl,
                        method: 'POST',
                        dataType: 'json',
                        data: { _token: csrf, name: name }
                    }).done(function (response) {
                        addOptionToGroup(select.dataset.group, response.data.name);
                        select.value = response.data.name;
                        $(select).trigger('change.select2');
                    }).fail(function (xhr) {
                        var message = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not save this.';
                        window.alert(message);
                    });
                }

                window.icpPopulateCreatableOptions = function (select) {
                    var group = select.dataset.group;
                    if (!group || !creatableOptions[group]) { return; }

                    creatableOptions[group].forEach(function (name) {
                        insertOption(select, name);
                    });
                };

                window.icpInitSelect2 = function (select) {
                    var $select = $(select);
                    if ($select.data('select2')) { return; }

                    var isCreatable = select.classList.contains('js-creatable-select');
                    var placeholder = ($select.find('option[value=""]').text() || 'Select…').replace('…', '');

                    $select.select2({
                        width: '100%',
                        tags: isCreatable,
                        placeholder: placeholder,
                        allowClear: false,
                        createTag: function (params) {
                            var term = $.trim(params.term);
                            if (!term || term === ADD_NEW) { return null; }
                            return { id: term, text: term, newTag: true };
                        }
                    });

                    if (isCreatable) {
                        $select.on('select2:select', function (e) {
                            var data = e.params.data;

                            if (data.id === ADD_NEW) {
                                var label = select.dataset.group === 'institute' ? 'institute' : 'faculty';
                                var name = window.prompt('New ' + label + ' name:');
                                name = name ? $.trim(name) : '';

                                if (name) {
                                    persistNewOption(select, name);
                                } else {
                                    select.value = '';
                                    $(select).trigger('change.select2');
                                }
                                return;
                            }

                            if (data.newTag) {
                                persistNewOption(select, data.id);
                            }
                        });
                    }
                };

                document.querySelectorAll('.js-document-type-select, .js-creatable-select').forEach(window.icpInitSelect2);
            })();

            // ── Multi-file inputs: preview newly-picked files, with the option
            //    to drop one before it's ever uploaded ──────────────────────────
            (function () {
                function renderPreviews(input) {
                    var container = input.nextElementSibling;
                    if (!container || !container.classList.contains('js-new-documents-preview')) {
                        return;
                    }

                    container.innerHTML = '';

                    Array.prototype.forEach.call(input.files, function (file, index) {
                        var chip = document.createElement('span');
                        chip.className = 'icp-chip d-inline-flex align-items-center gap-2 px-2 py-1 border rounded-3 small';

                        var img = document.createElement('img');
                        img.alt = file.name;
                        img.style.cssText = 'width:28px;height:28px;object-fit:cover;border-radius:4px;flex-shrink:0';
                        chip.appendChild(img);

                        var reader = new FileReader();
                        reader.onload = function (e) { img.src = e.target.result; };
                        reader.readAsDataURL(file);

                        var name = document.createElement('span');
                        name.textContent = file.name;
                        name.style.cssText = 'max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap';
                        chip.appendChild(name);

                        var removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.className = 'btn-close';
                        removeButton.style.fontSize = '.6rem';
                        removeButton.setAttribute('aria-label', 'Remove');
                        removeButton.addEventListener('click', function () {
                            var dataTransfer = new DataTransfer();
                            Array.prototype.forEach.call(input.files, function (keptFile, keptIndex) {
                                if (keptIndex !== index) { dataTransfer.items.add(keptFile); }
                            });
                            input.files = dataTransfer.files;
                            renderPreviews(input);
                        });
                        chip.appendChild(removeButton);

                        container.appendChild(chip);
                    });
                }

                document.addEventListener('change', function (e) {
                    var input = e.target.closest('.js-multi-file-input');
                    if (input) {
                        renderPreviews(input);
                    }
                });
            })();

            // ── Photo picker: live preview in the same circular crop as the PDF ──
            (function () {
                var photoInput = document.getElementById('photo');
                var preview = document.getElementById('photo-preview');
                var placeholder = document.getElementById('photo-placeholder');

                if (!photoInput || !preview) { return; }

                photoInput.addEventListener('change', function () {
                    var file = photoInput.files && photoInput.files[0];
                    if (!file) { return; }

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        if (placeholder) { placeholder.style.display = 'none'; }
                    };
                    reader.readAsDataURL(file);
                });
            })();

            // ── Citizenship / passport number input masks ─────────────────────
            (function () {
                if (!$.fn.inputmask) { return; }

                $('#citizenship_number').inputmask('99-99-99-99999', { placeholder: '_', clearIncomplete: false });
                $('#passport_number').inputmask({
                    mask: 'aa9999999',
                    placeholder: '_',
                    casing: 'upper',
                    clearIncomplete: false
                });

                $('#dob_bs, #citizenship_issued_date, #passport_issued_date').inputmask('9999-99-99', {
                    placeholder: 'yyyy-mm-dd',
                    clearIncomplete: false
                });
            })();

            // ── Course & Intake: keep Level in sync with the selected course ──
            (function () {
                var courseSelect = document.getElementById('course_id');
                var levelSelect = document.getElementById('level');

                function populateLevels(preserveValue) {
                    var selectedOption = courseSelect.options[courseSelect.selectedIndex];
                    var levels = [];
                    try {
                        levels = selectedOption && selectedOption.dataset.levels ? JSON.parse(selectedOption.dataset.levels) : [];
                    } catch (e) {
                        levels = [];
                    }

                    var currentValue = preserveValue ? levelSelect.value : null;

                    levelSelect.innerHTML = '';

                    var placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.disabled = true;
                    placeholder.textContent = levels.length ? 'Select a level…' : 'Select a course first…';
                    levelSelect.appendChild(placeholder);

                    var matched = false;
                    levels.forEach(function (level) {
                        var option = document.createElement('option');
                        option.value = level;
                        option.textContent = level;
                        if (currentValue && currentValue === level) {
                            option.selected = true;
                            matched = true;
                        }
                        levelSelect.appendChild(option);
                    });

                    placeholder.selected = ! matched;
                }

                courseSelect.addEventListener('change', function () {
                    populateLevels(false);
                });
            })();

            // ── Digital signature (signotec pad, Default mode) ────────────────
            (function () {
                var STPadServerLibCommons = window.STPadServerLib && window.STPadServerLib.STPadServerLibCommons;
                var STPadServerLibDefault = window.STPadServerLib && window.STPadServerLib.STPadServerLibDefault;

                var wsUrl = 'wss://127.0.0.1:49494'; // must match this PC's signotec service install option

                var statusText = document.getElementById('signotec-status-text');
                var startButton = document.getElementById('js-signotec-start');
                var doneButton = document.getElementById('js-signotec-done');
                var retryButton = document.getElementById('js-signotec-retry');
                var preview = document.getElementById('signotec-preview');
                var previewLabel = document.getElementById('signotec-preview-label');
                var signatureInput = document.getElementById('signature-input');

                var padIndex = null;

                function setStatus(message) {
                    statusText.textContent = message;
                }

                function describeError(prefix, error) {
                    console.error('signotec error:', error);
                    return prefix + (error && error.errorMessage ? ' (' + error.errorMessage + ')' : '');
                }

                function connect() {
                    STPadServerLibCommons.createConnection(wsUrl, onOpen, onClose, onError);
                }

                async function onOpen() {
                    try {
                        setStatus('Looking for the signature pad...');
                        var found = await STPadServerLibDefault.searchForPads(new STPadServerLibDefault.Params.searchForPads());

                        if (!found.foundPads.length) {
                            setStatus('No signotec pad found. Check the USB connection and try again.');
                            startButton.disabled = false;
                            return;
                        }

                        padIndex = found.foundPads[0].index;
                        await STPadServerLibDefault.openPad(new STPadServerLibDefault.Params.openPad(padIndex));

                        var sigParams = new STPadServerLibDefault.Params.startSignature();
                        sigParams.setFieldName('Signature');
                        await STPadServerLibDefault.startSignature(sigParams);

                        setStatus('Sign on the pad now, then press "Done" here.');
                        startButton.style.display = 'none';
                        doneButton.style.display = 'inline-flex';
                        retryButton.style.display = 'inline-block';
                    } catch (error) {
                        setStatus(describeError('Could not start signing on the pad.', error));
                        startButton.disabled = false;
                    }
                }

                function onClose() {
                    setStatus('Connection to the pad was closed.');
                }

                function onError() {
                    setStatus('Could not reach the signotec service. Is it running on this PC?');
                    startButton.disabled = false;
                }

                startButton.addEventListener('click', function () {
                    startButton.disabled = true;
                    setStatus('Connecting to the signotec pad…');
                    connect();
                });

                doneButton.addEventListener('click', async function () {
                    try {
                        var confirmation = await STPadServerLibDefault.confirmSignature();
                        if (confirmation.countedPoints < 5) {
                            setStatus('That looks empty — please sign again.');
                            await STPadServerLibDefault.retrySignature();
                            return;
                        }

                        var imageParams = new STPadServerLibDefault.Params.getSignatureImage();
                        imageParams.setFileType(STPadServerLibDefault.FileType.PNG);
                        var image = await STPadServerLibDefault.getSignatureImage(imageParams);
                        var dataUrl = 'data:image/png;base64,' + image.file;

                        signatureInput.value = dataUrl;
                        preview.src = dataUrl;
                        preview.style.display = 'block';
                        previewLabel.style.display = 'block';

                        await STPadServerLibDefault.closePad(new STPadServerLibDefault.Params.closePad(padIndex));
                        STPadServerLibCommons.destroyConnection();

                        setStatus('Signature captured. Reload this page to sign again.');
                        doneButton.style.display = 'none';
                        retryButton.style.display = 'none';
                    } catch (error) {
                        setStatus(describeError('Could not read the signature from the pad.', error));
                    }
                });

                retryButton.addEventListener('click', async function () {
                    try {
                        await STPadServerLibDefault.retrySignature();
                        setStatus('Cleared — please sign again.');
                    } catch (error) {
                        setStatus(describeError('Could not clear the pad.', error));
                    }
                });

                if (!STPadServerLibCommons || !STPadServerLibDefault) {
                    setStatus('signotec pad library failed to load.');
                    startButton.disabled = true;
                }
            })();

            // ── Stepper ──────────────────────────────────────────────────────
            // If the page reloaded with validation errors, show every step at
            // once instead of hiding whichever one holds the offending field.
            var hasErrors = document.querySelector('.is-invalid') !== null;
            var steps = Array.prototype.slice.call(document.querySelectorAll('.form-step'));
            var stepButtons = Array.prototype.slice.call(document.querySelectorAll('.js-step-nav'));
            var submitButton = document.getElementById('icp-form-submit');
            var prevButton = document.getElementById('js-step-prev');
            var nextButton = document.getElementById('js-step-next');
            var current = 1;

            function validateStep(number) {
                var stepEl = steps[number - 1];
                var inputs = stepEl.querySelectorAll('input, select, textarea');
                for (var i = 0; i < inputs.length; i++) {
                    if (!inputs[i].checkValidity()) {
                        // Select2 hides the real <select>, so a native
                        // validation bubble has nothing to anchor to — open
                        // the dropdown instead so the gap is obvious.
                        if (inputs[i].classList.contains('select2-hidden-accessible')) {
                            $(inputs[i]).select2('open');
                        } else {
                            inputs[i].reportValidity();
                        }
                        return false;
                    }
                }
                return true;
            }

            function showStep(number) {
                current = number;

                steps.forEach(function (el) {
                    el.style.display = (parseInt(el.dataset.step, 10) === number) ? '' : 'none';
                });

                stepButtons.forEach(function (btn) {
                    var active = parseInt(btn.dataset.step, 10) === number;
                    btn.querySelector('.js-step-circle').style.background = active ? '#8D2229' : 'var(--background-color)';
                    btn.querySelector('.js-step-circle').style.color = active ? '#fff' : 'var(--general-color)';
                    btn.querySelector('.js-step-label').style.fontWeight = active ? '700' : '400';
                });

                prevButton.style.display = number === 1 ? 'none' : '';
                nextButton.style.display = number === steps.length ? 'none' : '';
                if (submitButton) {
                    submitButton.style.display = number === steps.length ? '' : 'none';
                }
            }

            if (hasErrors || steps.length === 0) {
                // These two elements carry Bootstrap's `.d-flex` (display:flex
                // !important), which beats a plain inline style — toggle the
                // `.d-none` utility class instead so it actually hides.
                document.getElementById('form-stepper').classList.add('d-none');
                document.getElementById('form-stepper-nav').classList.add('d-none');
            } else {
                nextButton.addEventListener('click', function () {
                    if (validateStep(current) && current < steps.length) {
                        showStep(current + 1);
                    }
                });

                prevButton.addEventListener('click', function () {
                    if (current > 1) { showStep(current - 1); }
                });

                stepButtons.forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        showStep(parseInt(btn.dataset.step, 10));
                    });
                });

                showStep(1);
            }
        })(jQuery);
    </script>
@endpush
