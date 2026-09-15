@php
    $item = $item ?? null;
    $qualifications = $item?->qualifications ?? collect();
    $formSteps = [
        1 => 'Student Info',
        2 => 'Contact',
        3 => 'Parent / Guardian',
        4 => 'Academic',
        5 => 'Course & Intake',
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

{{-- ── Student Information ── --}}
<div class="icp-form-section form-step" data-step="1">
    <div class="icp-card-header">
        <p class="icp-card-title">Student Information</p>
    </div>
    <div class="icp-form-section-body">
        <div class="row g-4">
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
                <label for="certificate_name" class="form-label fw-semibold small icp-label">Name appearing on certificate</label>
                <input id="certificate_name" name="certificate_name" type="text" required
                    value="{{ old('certificate_name', $item?->certificate_name) }}"
                    class="form-control icp-input @error('certificate_name') is-invalid @enderror">
                @error('certificate_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                    value="{{ old('citizenship_number', $item?->citizenship_number) }}"
                    class="form-control icp-input @error('citizenship_number') is-invalid @enderror">
                @error('citizenship_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6">
                <label for="citizenship_issued_date" class="form-label fw-semibold small icp-label">Citizenship issued date</label>
                <input id="citizenship_issued_date" name="citizenship_issued_date" type="date"
                    value="{{ old('citizenship_issued_date', $item?->citizenship_issued_date?->format('Y-m-d')) }}"
                    class="form-control icp-input @error('citizenship_issued_date') is-invalid @enderror">
                @error('citizenship_issued_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6">
                <label for="passport_number" class="form-label fw-semibold small icp-label">Passport number</label>
                <input id="passport_number" name="passport_number" type="text"
                    value="{{ old('passport_number', $item?->passport_number) }}"
                    class="form-control icp-input @error('passport_number') is-invalid @enderror">
                @error('passport_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6">
                <label for="passport_issued_date" class="form-label fw-semibold small icp-label">Passport issued date</label>
                <input id="passport_issued_date" name="passport_issued_date" type="date"
                    value="{{ old('passport_issued_date', $item?->passport_issued_date?->format('Y-m-d')) }}"
                    class="form-control icp-input @error('passport_issued_date') is-invalid @enderror">
                @error('passport_issued_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6">
                <label for="declared_date" class="form-label fw-semibold small icp-label">Declared date</label>
                <input id="declared_date" name="declared_date" type="date" required
                    value="{{ old('declared_date', $item?->declared_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                    class="form-control icp-input @error('declared_date') is-invalid @enderror">
                @error('declared_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6">
                <label for="photo" class="form-label fw-semibold small icp-label">Photo <span class="text-muted fw-normal">(JPG, optional)</span></label>
                <input id="photo" name="photo" type="file" accept="image/jpeg"
                    class="form-control icp-input @error('photo') is-invalid @enderror">
                @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if($item?->photo_path)
                    <div class="form-text small">
                        Current: <img src="{{ Storage::url($item->photo_path) }}" alt="Current photo" style="height:28px;border-radius:6px;vertical-align:middle">
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Contact Information ── --}}
<div class="icp-form-section form-step" data-step="2">
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
                <label for="corresponding_address" class="form-label fw-semibold small icp-label">Corresponding address</label>
                <input id="corresponding_address" name="corresponding_address" type="text" required
                    value="{{ old('corresponding_address', $item?->corresponding_address) }}"
                    class="form-control icp-input @error('corresponding_address') is-invalid @enderror">
                @error('corresponding_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-4">
                <label for="mobile" class="form-label fw-semibold small icp-label">Mobile</label>
                <input id="mobile" name="mobile" type="text" required
                    value="{{ old('mobile', $item?->mobile) }}"
                    class="form-control icp-input @error('mobile') is-invalid @enderror">
                @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="email_1" class="form-label fw-semibold small icp-label">Email address</label>
                <input id="email_1" name="email_1" type="email" required
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
<div class="icp-form-section form-step" data-step="3">
    <div class="icp-card-header">
        <p class="icp-card-title">Parent / Guardian Information</p>
    </div>
    <div class="icp-form-section-body">
        <div class="row g-4">
            <div class="col-12 col-sm-4">
                <label for="father_full_name" class="form-label fw-semibold small icp-label">Father's full name</label>
                <input id="father_full_name" name="father_full_name" type="text" required
                    value="{{ old('father_full_name', $item?->father_full_name) }}"
                    class="form-control icp-input @error('father_full_name') is-invalid @enderror">
                @error('father_full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="father_mobile" class="form-label fw-semibold small icp-label">Father's mobile</label>
                <input id="father_mobile" name="father_mobile" type="text" required
                    value="{{ old('father_mobile', $item?->father_mobile) }}"
                    class="form-control icp-input @error('father_mobile') is-invalid @enderror">
                @error('father_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="father_email" class="form-label fw-semibold small icp-label">Father's email <span class="text-muted fw-normal">(optional)</span></label>
                <input id="father_email" name="father_email" type="email"
                    value="{{ old('father_email', $item?->father_email) }}"
                    class="form-control icp-input @error('father_email') is-invalid @enderror">
                @error('father_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-4">
                <label for="mother_full_name" class="form-label fw-semibold small icp-label">Mother's full name</label>
                <input id="mother_full_name" name="mother_full_name" type="text" required
                    value="{{ old('mother_full_name', $item?->mother_full_name) }}"
                    class="form-control icp-input @error('mother_full_name') is-invalid @enderror">
                @error('mother_full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="mother_mobile" class="form-label fw-semibold small icp-label">Mother's mobile</label>
                <input id="mother_mobile" name="mother_mobile" type="text" required
                    value="{{ old('mother_mobile', $item?->mother_mobile) }}"
                    class="form-control icp-input @error('mother_mobile') is-invalid @enderror">
                @error('mother_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="mother_email" class="form-label fw-semibold small icp-label">Mother's email <span class="text-muted fw-normal">(optional)</span></label>
                <input id="mother_email" name="mother_email" type="email"
                    value="{{ old('mother_email', $item?->mother_email) }}"
                    class="form-control icp-input @error('mother_email') is-invalid @enderror">
                @error('mother_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-4">
                <label for="guardian_full_name" class="form-label fw-semibold small icp-label">Local guardian's full name <span class="text-muted fw-normal">(optional)</span></label>
                <input id="guardian_full_name" name="guardian_full_name" type="text"
                    value="{{ old('guardian_full_name', $item?->guardian_full_name) }}"
                    class="form-control icp-input @error('guardian_full_name') is-invalid @enderror">
                @error('guardian_full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="guardian_contact" class="form-label fw-semibold small icp-label">Local guardian's contact <span class="text-muted fw-normal">(optional)</span></label>
                <input id="guardian_contact" name="guardian_contact" type="text"
                    value="{{ old('guardian_contact', $item?->guardian_contact) }}"
                    class="form-control icp-input @error('guardian_contact') is-invalid @enderror">
                @error('guardian_contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="guardian_email" class="form-label fw-semibold small icp-label">Local guardian's email <span class="text-muted fw-normal">(optional)</span></label>
                <input id="guardian_email" name="guardian_email" type="email"
                    value="{{ old('guardian_email', $item?->guardian_email) }}"
                    class="form-control icp-input @error('guardian_email') is-invalid @enderror">
                @error('guardian_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- ── Academic / Education Information ── --}}
<div class="icp-form-section form-step" data-step="4">
    <div class="icp-card-header">
        <p class="icp-card-title">Academic / Education Information</p>
        <p class="icp-card-subtitle">The highest / primary qualification below, plus any additional qualifications on file.</p>
    </div>
    <div class="icp-form-section-body">
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-4">
                <label for="highest_qualification" class="form-label fw-semibold small icp-label">Highest qualification</label>
                <input id="highest_qualification" name="highest_qualification" type="text" required placeholder="e.g. NEB"
                    value="{{ old('highest_qualification', $item?->highest_qualification) }}"
                    class="form-control icp-input @error('highest_qualification') is-invalid @enderror">
                @error('highest_qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="awarding_body" class="form-label fw-semibold small icp-label">Awarding body</label>
                <input id="awarding_body" name="awarding_body" type="text" required
                    value="{{ old('awarding_body', $item?->awarding_body) }}"
                    class="form-control icp-input @error('awarding_body') is-invalid @enderror">
                @error('awarding_body') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="qualification_description" class="form-label fw-semibold small icp-label">Qualification description <span class="text-muted fw-normal">(optional)</span></label>
                <input id="qualification_description" name="qualification_description" type="text"
                    value="{{ old('qualification_description', $item?->qualification_description) }}"
                    class="form-control icp-input @error('qualification_description') is-invalid @enderror">
                @error('qualification_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <p class="fw-semibold small mb-2">Additional qualifications</p>
        <div id="qualifications-container" class="d-flex flex-column gap-3 mb-3">
            @foreach($qualifications as $qualification)
                <div class="qualification-row border rounded-3 p-3 position-relative">
                    <button type="button" class="btn-close js-remove-row position-absolute top-0 end-0 m-2" aria-label="Remove"></button>
                    <input type="hidden" name="qualifications[{{ $loop->index }}][existing_document_path]" value="{{ $qualification->document_path }}">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label small icp-label">Document type</label>
                            <input type="text" name="qualifications[{{ $loop->index }}][document_type]" value="{{ $qualification->document_type }}" required class="form-control icp-input">
                        </div>
                        <div class="col-6 col-sm-3 col-lg-2">
                            <label class="form-label small icp-label">Awarded year</label>
                            <input type="number" name="qualifications[{{ $loop->index }}][awarded_year]" value="{{ $qualification->awarded_year }}" class="form-control icp-input">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label class="form-label small icp-label">Subject</label>
                            <input type="text" name="qualifications[{{ $loop->index }}][subject]" value="{{ $qualification->subject }}" required class="form-control icp-input">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label small icp-label">Institute name</label>
                            <input type="text" name="qualifications[{{ $loop->index }}][institute_name]" value="{{ $qualification->institute_name }}" required class="form-control icp-input">
                        </div>
                        <div class="col-6 col-sm-3 col-lg-2">
                            <label class="form-label small icp-label">Score</label>
                            <input type="text" name="qualifications[{{ $loop->index }}][score]" value="{{ $qualification->score }}" class="form-control icp-input">
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="form-label small icp-label">Document <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="file" name="qualifications[{{ $loop->index }}][document]" class="form-control icp-input">
                            @if($qualification->document_path)
                                <div class="form-text small">Current file on record &mdash; upload a new one to replace it.</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" id="add-qualification" class="btn icp-btn-muted d-inline-flex align-items-center gap-2 px-3 py-2">
            @svg('heroicon-m-plus', 'icp-icon-sm')
            <span>Add Qualification</span>
        </button>

        <template id="qualification-row-template">
            <div class="qualification-row border rounded-3 p-3 position-relative">
                <button type="button" class="btn-close js-remove-row position-absolute top-0 end-0 m-2" aria-label="Remove"></button>
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small icp-label">Document type</label>
                        <input type="text" name="qualifications[__INDEX__][document_type]" required class="form-control icp-input">
                    </div>
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="form-label small icp-label">Awarded year</label>
                        <input type="number" name="qualifications[__INDEX__][awarded_year]" class="form-control icp-input">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small icp-label">Subject</label>
                        <input type="text" name="qualifications[__INDEX__][subject]" required class="form-control icp-input">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label small icp-label">Institute name</label>
                        <input type="text" name="qualifications[__INDEX__][institute_name]" required class="form-control icp-input">
                    </div>
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="form-label small icp-label">Score</label>
                        <input type="text" name="qualifications[__INDEX__][score]" class="form-control icp-input">
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label small icp-label">Document <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="file" name="qualifications[__INDEX__][document]" class="form-control icp-input">
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

{{-- ── Course & Intake Information ── --}}
<div class="icp-form-section form-step" data-step="5">
    <div class="icp-card-header">
        <p class="icp-card-title">Course &amp; Intake Information</p>
        @if($item)
            <p class="icp-card-subtitle">Student ID <strong>{{ $item->admission_id }}</strong> &middot; Group <strong>{{ $item->group }}</strong> (assigned automatically, not editable)</p>
        @else
            <p class="icp-card-subtitle">The Student ID and class group are assigned automatically once this form is submitted.</p>
        @endif
    </div>
    <div class="icp-form-section-body">
        <div class="row g-4">
            <div class="col-12 col-sm-6">
                <label for="course_id" class="form-label fw-semibold small icp-label">Course</label>
                <select id="course_id" name="course_id" required class="form-select icp-input @error('course_id') is-invalid @enderror">
                    <option value="" disabled @selected(! old('course_id', $item?->course_id))>Select a course&hellip;</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id', $item?->course_id) === $course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
                @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6">
                <label for="admission_year_id" class="form-label fw-semibold small icp-label">Intake (Admission Year)</label>
                <select id="admission_year_id" name="admission_year_id" required class="form-select icp-input @error('admission_year_id') is-invalid @enderror">
                    <option value="" disabled @selected(! old('admission_year_id', $item?->admission_year_id))>Select an intake&hellip;</option>
                    @foreach($admissionYears as $admissionYear)
                        <option value="{{ $admissionYear->id }}" @selected(old('admission_year_id', $item?->admission_year_id) === $admissionYear->id)>{{ $admissionYear->title }}</option>
                    @endforeach
                </select>
                @error('admission_year_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-4">
                <label for="level" class="form-label fw-semibold small icp-label">Level</label>
                <input id="level" name="level" type="text" required
                    value="{{ old('level', $item?->level) }}"
                    class="form-control icp-input @error('level') is-invalid @enderror">
                @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="entry_type" class="form-label fw-semibold small icp-label">Entry type</label>
                <input id="entry_type" name="entry_type" type="text" required placeholder="e.g. Standard"
                    value="{{ old('entry_type', $item?->entry_type) }}"
                    class="form-control icp-input @error('entry_type') is-invalid @enderror">
                @error('entry_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-4">
                <label for="semester" class="form-label fw-semibold small icp-label">Semester</label>
                <select id="semester" name="semester" required class="form-select icp-input @error('semester') is-invalid @enderror">
                    @foreach(['Spring', 'Summer', 'Autumn'] as $option)
                        <option value="{{ $option }}" @selected(old('semester', $item?->semester) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-sm-6">
                <label for="biometric_id" class="form-label fw-semibold small icp-label">Biometric ID <span class="text-muted fw-normal">(optional, filled in later)</span></label>
                <input id="biometric_id" name="biometric_id" type="text"
                    value="{{ old('biometric_id', $item?->biometric_id) }}"
                    class="form-control icp-input @error('biometric_id') is-invalid @enderror">
                @error('biometric_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6">
                <label for="university_registration_no" class="form-label fw-semibold small icp-label">University registration no. (LMU) <span class="text-muted fw-normal">(optional, filled in later)</span></label>
                <input id="university_registration_no" name="university_registration_no" type="text"
                    value="{{ old('university_registration_no', $item?->university_registration_no) }}"
                    class="form-control icp-input @error('university_registration_no') is-invalid @enderror">
                @error('university_registration_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
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
        <p class="icp-card-subtitle">Attach scanned documents such as citizenship/passport and academic transcripts.</p>
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
        <div id="documents-container" class="d-flex flex-column gap-3 mb-3"></div>
        <div class="d-flex flex-wrap gap-2 mb-1">
            <button type="button" class="btn icp-btn-muted d-inline-flex align-items-center gap-2 px-3 py-2 js-add-document" data-title="Citizenship">
                @svg('heroicon-m-plus', 'icp-icon-sm')<span>Citizenship</span>
            </button>
            <button type="button" class="btn icp-btn-muted d-inline-flex align-items-center gap-2 px-3 py-2 js-add-document" data-title="Academic Documents">
                @svg('heroicon-m-plus', 'icp-icon-sm')<span>Academic Documents</span>
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
                        <label class="form-label small icp-label">File</label>
                        <input type="file" name="documents[__INDEX__][file]" required class="form-control icp-input">
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

@push('scripts')
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
                    container.appendChild(fragment);
                    nextIndex++;
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

            makeRepeater('qualifications-container', 'qualification-row-template', '#add-qualification', {{ $qualifications->count() }});
            makeRepeater('documents-container', 'document-row-template', '.js-add-document', 0);

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
                        inputs[i].reportValidity();
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
                document.getElementById('form-stepper').style.display = 'none';
                document.getElementById('form-stepper-nav').style.display = 'none';
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
