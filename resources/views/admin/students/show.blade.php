@extends('admin.templates.show')

@section('title', 'View '.$title)

@section('form_content')

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            @if($item->photo_path)
                <img src="{{ Storage::url($item->photo_path) }}" alt="{{ $item->fullName() }}"
                    class="rounded-circle border" style="width:64px;height:64px;object-fit:cover">
            @endif
            <div>
                <h2 class="mb-0" style="font-size:1.1rem">{{ $item->fullName() }}</h2>
                <p class="mb-0 small text-muted">Student ID <strong>{{ $item->enrollment?->admission_id }}</strong> &middot; Group <strong>{{ $item->group }}</strong></p>
            </div>
        </div>
        @can('students.export-pdf')
            <a href="{{ route('admin.students.export-pdf', $item->getKey()) }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                @svg('heroicon-m-document-arrow-down', 'icp-icon-sm')
                <span>Download PDF</span>
            </a>
        @endcan
    </div>

    {{-- ── Student Information ── --}}
    <div class="icp-card mb-4">
        <div class="icp-card-header"><p class="icp-card-title">Student Information</p></div>
        <dl class="row g-0 mb-0 icp-detail-list p-3">
            <dt class="col-12 col-sm-4">Certificate name</dt>
            <dd class="col-12 col-sm-8">{{ $item->certificate_name }}</dd>
            <dt class="col-12 col-sm-4">Gender</dt>
            <dd class="col-12 col-sm-8">{{ ucfirst($item->gender) }}</dd>
            <dt class="col-12 col-sm-4">Date of birth</dt>
            <dd class="col-12 col-sm-8">{{ $item->dob_ad?->format('d M, Y') }} @if($item->dob_bs)(BS {{ $item->dob_bs }})@endif</dd>
            <dt class="col-12 col-sm-4">Citizenship number</dt>
            <dd class="col-12 col-sm-8">{{ $item->citizenship_number ?: '—' }}</dd>
            <dt class="col-12 col-sm-4">Passport number</dt>
            <dd class="col-12 col-sm-8">{{ $item->passport_number ?: '—' }}</dd>
            <dt class="col-12 col-sm-4">Declared date</dt>
            <dd class="col-12 col-sm-8">{{ $item->enrollment?->declared_date?->format('d M, Y') }}</dd>
        </dl>
    </div>

    {{-- ── Contact Information ── --}}
    <div class="icp-card mb-4">
        <div class="icp-card-header"><p class="icp-card-title">Contact Information</p></div>
        <dl class="row g-0 mb-0 icp-detail-list p-3">
            <dt class="col-12 col-sm-4">Permanent address</dt>
            <dd class="col-12 col-sm-8">{{ $item->permanent_address }}</dd>
            <dt class="col-12 col-sm-4">Corresponding address</dt>
            <dd class="col-12 col-sm-8">{{ $item->corresponding_address }}</dd>
            <dt class="col-12 col-sm-4">Mobile</dt>
            <dd class="col-12 col-sm-8">{{ $item->mobile }}</dd>
            <dt class="col-12 col-sm-4">Email</dt>
            <dd class="col-12 col-sm-8">{{ $item->email_1 }} @if($item->email_2) / {{ $item->email_2 }} @endif</dd>
        </dl>
    </div>

    {{-- ── Parent / Guardian Information ── --}}
    <div class="icp-card mb-4">
        <div class="icp-card-header"><p class="icp-card-title">Parent / Guardian Information</p></div>
        <dl class="row g-0 mb-0 icp-detail-list p-3">
            <dt class="col-12 col-sm-4">Father</dt>
            <dd class="col-12 col-sm-8">{{ $item->father_full_name ? $item->father_full_name.' · '.$item->father_mobile : '—' }}</dd>
            <dt class="col-12 col-sm-4">Mother</dt>
            <dd class="col-12 col-sm-8">{{ $item->mother_full_name ? $item->mother_full_name.' · '.$item->mother_mobile : '—' }}</dd>
            <dt class="col-12 col-sm-4">Local guardian</dt>
            <dd class="col-12 col-sm-8">{{ $item->guardian_full_name ? $item->guardian_full_name.($item->guardian_relationship ? ' ('.$item->guardian_relationship.')' : '').' · '.$item->guardian_contact : '—' }}</dd>
        </dl>
    </div>

    {{-- ── Academic / Education Information ── --}}
    <div class="icp-card mb-4">
        <div class="icp-card-header"><p class="icp-card-title">Academic / Education Information</p></div>
        @if($item->qualifications->isEmpty())
            <p class="small text-muted p-3 mb-0">No qualifications on file.</p>
        @else
            <div class="table-responsive p-3">
                <table class="table icp-table mb-0">
                    <thead>
                        <tr>
                            <th>Educational Board</th><th>Year</th><th>Faculty</th><th>Institute</th><th>Score</th><th>Description</th><th>Documents</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->qualifications->sortByDesc('is_highest') as $qualification)
                            <tr>
                                <td>
                                    {{ $qualification->document_type }}
                                    @if($qualification->is_highest)
                                        <span class="badge bg-primary ms-1">Highest</span>
                                    @endif
                                </td>
                                <td>{{ $qualification->awarded_year }}</td>
                                <td>{{ $qualification->faculty }}</td>
                                <td>{{ $qualification->institute_name }}</td>
                                <td>{{ $qualification->score }}{{ $qualification->score_type ? ' ('.$qualification->score_type.')' : '' }}</td>
                                <td>{{ $qualification->qualification_description ?: '—' }}</td>
                                <td>
                                    @foreach($qualification->documents as $document)
                                        <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="me-1" title="{{ $document->original_filename }}">@svg('heroicon-m-paper-clip', 'icp-icon-sm')</a>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── Course & Intake Information ── --}}
    <div class="icp-card mb-4">
        <div class="icp-card-header"><p class="icp-card-title">Course &amp; Intake Information</p></div>
        <dl class="row g-0 mb-0 icp-detail-list p-3">
            <dt class="col-12 col-sm-4">Course</dt>
            <dd class="col-12 col-sm-8">{{ $item->course?->title }}</dd>
            <dt class="col-12 col-sm-4">Intake</dt>
            <dd class="col-12 col-sm-8">{{ $item->intake?->title }}</dd>
            <dt class="col-12 col-sm-4">Level / Entry type / Semester</dt>
            <dd class="col-12 col-sm-8">{{ $item->enrollment?->level }} &middot; {{ $item->enrollment?->entry_type }} &middot; {{ $item->enrollment?->semester }}</dd>
            <dt class="col-12 col-sm-4">Biometric ID</dt>
            <dd class="col-12 col-sm-8">{{ $item->biometric_id ?: '—' }}</dd>
            <dt class="col-12 col-sm-4">University registration no. (LMU)</dt>
            <dd class="col-12 col-sm-8">{{ $item->university_registration_no ?: '—' }}</dd>
        </dl>
    </div>

    {{-- ── Medical Background ── --}}
    <div class="icp-card mb-4">
        <div class="icp-card-header"><p class="icp-card-title">Medical Background</p></div>
        <dl class="row g-0 mb-0 icp-detail-list p-3">
            <dt class="col-12 col-sm-8">Disorder posing a threat to safety/welfare</dt>
            <dd class="col-12 col-sm-4">{{ $item->has_disorder ? 'Yes' : 'No' }}</dd>
            <dt class="col-12 col-sm-8">Drug abuser / addict</dt>
            <dd class="col-12 col-sm-4">{{ $item->is_drug_abuser ? 'Yes' : 'No' }}</dd>
            <dt class="col-12 col-sm-8">Arrested / convicted</dt>
            <dd class="col-12 col-sm-4">{{ $item->has_criminal_record ? 'Yes' : 'No' }}</dd>
            <dt class="col-12 col-sm-8">Communicable disease</dt>
            <dd class="col-12 col-sm-4">{{ $item->has_communicable_disease ? 'Yes' : 'No' }}</dd>
            <dt class="col-12 col-sm-8">Minor requiring consent</dt>
            <dd class="col-12 col-sm-4">{{ $item->is_minor_requiring_consent ? 'Yes' : 'No' }}</dd>
        </dl>
    </div>

    {{-- ── Documents ── --}}
    <div class="icp-card mb-4">
        <div class="icp-card-header"><p class="icp-card-title">Documents</p></div>
        <div class="p-3">
            @forelse($item->documents as $document)
                <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="d-flex align-items-center gap-2 text-decoration-none mb-2">
                    @svg('heroicon-m-paper-clip', 'icp-icon-sm')
                    <span>{{ $document->title }} &mdash; {{ $document->original_filename }}</span>
                </a>
            @empty
                <p class="small text-muted mb-0">No documents uploaded.</p>
            @endforelse
        </div>
    </div>

    {{-- ── Digital Signature ── --}}
    <div class="icp-card mb-4">
        <div class="icp-card-header"><p class="icp-card-title">Digital Signature</p></div>
        <div class="p-3">
            @if($item->signature_path)
                <img src="{{ Storage::url($item->signature_path) }}" alt="Signature" style="height:70px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:4px">
            @else
                <p class="small text-muted mb-0">No signature on file.</p>
            @endif
        </div>
    </div>

@endsection
