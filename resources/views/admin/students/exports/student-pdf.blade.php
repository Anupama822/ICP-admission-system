<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $student->admission_id }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 28px;
            font-family: Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 11px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 18px;
        }
        .header .logo { display: table-cell; width: 60px; vertical-align: middle; }
        .header .logo img { width: 50px; }
        .header .brand { display: table-cell; vertical-align: middle; padding-left: 10px; }
        .header .brand .name { font-size: 18px; font-weight: bold; color: #8D2229; }
        .header .brand .tagline { font-size: 10px; color: #676767; }
        .photo-block { display: table-cell; width: 110px; vertical-align: top; text-align: center; }
        .photo-block img.photo { width: 90px; height: 90px; object-fit: cover; border-radius: 50%; border: 1px solid #ddd; }
        .signature-block { margin-top: 8px; }
        .signature-block img { height: 40px; }

        .section {
            border: 1px solid #f0f1f3;
            border-radius: 6px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .section-title {
            background: rgba(141, 34, 41, .06);
            color: #8D2229;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 6px 10px;
            border-bottom: 1px solid #f0f1f3;
        }
        .section-body { padding: 8px 10px; }
        table.fields { width: 100%; border-collapse: collapse; }
        table.fields td { padding: 4px 6px; vertical-align: top; width: 50%; }
        table.fields .label { color: #676767; font-size: 9.5px; text-transform: uppercase; letter-spacing: .03em; }
        table.fields .value { font-weight: bold; font-size: 11px; }

        table.qualifications { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.qualifications th, table.qualifications td { border: 1px solid #f0f1f3; padding: 5px 6px; font-size: 10px; text-align: left; }
        table.qualifications th { background: #fbfbfb; text-transform: uppercase; font-size: 9px; color: #676767; }

        .footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 2px solid #8D2229;
            font-size: 9px;
            color: #676767;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo"><img src="{{ public_path('images/logo.png') }}" alt="Logo"></div>
        <div class="brand">
            <div class="name">Informatics College Pokhara</div>
            <div class="tagline">Student Enrollment Form &middot; Admission ID: {{ $student->admission_id }} &middot; Group: {{ $student->group }}</div>
        </div>
        @if($student->photo_path && Storage::disk('public')->exists($student->photo_path))
            <div class="photo-block">
                <img class="photo" src="{{ Storage::disk('public')->path($student->photo_path) }}" alt="Photo">
            </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Personal Details</div>
        <div class="section-body">
            <table class="fields">
                <tr>
                    <td><div class="label">Full Name</div><div class="value">{{ $student->fullName() }}</div></td>
                    <td><div class="label">Name on Certificate</div><div class="value">{{ $student->certificate_name }}</div></td>
                </tr>
                <tr>
                    <td><div class="label">Gender</div><div class="value">{{ ucfirst($student->gender) }}</div></td>
                    <td><div class="label">Date of Birth (AD / BS)</div><div class="value">{{ $student->dob_ad?->format('Y-m-d') }} @if($student->dob_bs)/ {{ $student->dob_bs }} @endif</div></td>
                </tr>
                <tr>
                    <td><div class="label">Citizenship Number</div><div class="value">{{ $student->citizenship_number ?: '--' }}</div></td>
                    <td><div class="label">Passport Number</div><div class="value">{{ $student->passport_number ?: '--' }}</div></td>
                </tr>
                <tr>
                    <td><div class="label">Declared Date</div><div class="value">{{ $student->declared_date?->format('Y-m-d') }}</div></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Course Details</div>
        <div class="section-body">
            <table class="fields">
                <tr>
                    <td><div class="label">Course</div><div class="value">{{ $student->course?->title }}</div></td>
                    <td><div class="label">Level</div><div class="value">{{ $student->level }}</div></td>
                </tr>
                <tr>
                    <td><div class="label">Entry Type</div><div class="value">{{ $student->entry_type }}</div></td>
                    <td><div class="label">Semester</div><div class="value">{{ $student->semester }}</div></td>
                </tr>
                <tr>
                    <td><div class="label">Intake Year</div><div class="value">{{ $student->intake?->title }}</div></td>
                    <td><div class="label">Group</div><div class="value">{{ $student->group }}</div></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Address &amp; Contact Details</div>
        <div class="section-body">
            <table class="fields">
                <tr>
                    <td><div class="label">Permanent Address</div><div class="value">{{ $student->permanent_address }}</div></td>
                    <td><div class="label">Corresponding Address</div><div class="value">{{ $student->corresponding_address }}</div></td>
                </tr>
                <tr>
                    <td><div class="label">Mobile</div><div class="value">{{ $student->mobile }}</div></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Email Address</div>
        <div class="section-body">
            <table class="fields">
                <tr>
                    <td><div class="label">Email Address 1</div><div class="value">{{ $student->email_1 }}</div></td>
                    <td><div class="label">Email Address 2</div><div class="value">{{ $student->email_2 ?: '--' }}</div></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Parent Details</div>
        <div class="section-body">
            <table class="fields">
                <tr>
                    <td><div class="label">Father's Full Name</div><div class="value">{{ $student->father_full_name ?: '--' }}</div></td>
                    <td><div class="label">Father's Mobile Number</div><div class="value">{{ $student->father_mobile ?: '--' }}</div></td>
                </tr>
                <tr>
                    <td><div class="label">Mother's Full Name</div><div class="value">{{ $student->mother_full_name ?: '--' }}</div></td>
                    <td><div class="label">Mother's Mobile Number</div><div class="value">{{ $student->mother_mobile ?: '--' }}</div></td>
                </tr>
                <tr>
                    <td><div class="label">Local Guardian's Full Name</div><div class="value">{{ $student->guardian_full_name ?: '--' }}</div></td>
                    <td><div class="label">Local Guardian's Contact Number</div><div class="value">{{ $student->guardian_contact ?: '--' }}</div></td>
                </tr>
                <tr>
                    <td><div class="label">Local Guardian's Relationship to Student</div><div class="value">{{ $student->guardian_relationship ?: '--' }}</div></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Academic Details</div>
        <div class="section-body">
            @if($student->qualifications->isEmpty())
                <p>No qualifications on file.</p>
            @else
                <table class="qualifications">
                    <thead>
                        <tr>
                            <th>Educational Board</th><th>Awarded Year</th><th>Faculty</th><th>Institute Name</th><th>Score</th><th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->qualifications->sortByDesc('is_highest') as $qualification)
                            <tr>
                                <td>{{ $qualification->document_type }}{{ $qualification->is_highest ? ' (Highest)' : '' }}</td>
                                <td>{{ $qualification->awarded_year }}</td>
                                <td>{{ $qualification->faculty }}</td>
                                <td>{{ $qualification->institute_name }}</td>
                                <td>{{ $qualification->score }}{{ $qualification->score_type ? ' ('.$qualification->score_type.')' : '' }}</td>
                                <td>{{ $qualification->qualification_description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Documents</div>
        <div class="section-body">
            @php
                $academicDocuments = $student->qualifications->flatMap(fn ($qualification) => $qualification->documents->map(
                    fn ($document) => ['label' => $qualification->document_type.' — '.$qualification->faculty, 'document' => $document]
                ));
                $otherDocuments = $student->documents;
            @endphp

            @if($academicDocuments->isEmpty() && $otherDocuments->isEmpty())
                <p>No documents on file.</p>
            @else
                @unless($academicDocuments->isEmpty())
                    <div class="label" style="margin-bottom:4px">Academic Documents</div>
                    <table class="fields" style="margin-bottom:8px">
                        @foreach($academicDocuments as $entry)
                            <tr>
                                <td colspan="2"><div class="value">{{ $entry['label'] }} &mdash; {{ $entry['document']->original_filename }}</div></td>
                            </tr>
                        @endforeach
                    </table>
                @endunless

                @unless($otherDocuments->isEmpty())
                    <div class="label" style="margin-bottom:4px">Other Documents</div>
                    <table class="fields">
                        @foreach($otherDocuments as $document)
                            <tr>
                                <td colspan="2"><div class="value">{{ $document->title }} &mdash; {{ $document->original_filename }}</div></td>
                            </tr>
                        @endforeach
                    </table>
                @endunless
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Medical Background</div>
        <div class="section-body">
            <table class="fields">
                <tr><td colspan="2">1. Mental or physical disorder posing a threat to safety/welfare? <strong>{{ $student->has_disorder ? 'Yes' : 'No' }}</strong></td></tr>
                <tr><td colspan="2">2. Drug abuser or addict? <strong>{{ $student->is_drug_abuser ? 'Yes' : 'No' }}</strong></td></tr>
                <tr><td colspan="2">3. Arrested or convicted for any offense or crime? <strong>{{ $student->has_criminal_record ? 'Yes' : 'No' }}</strong></td></tr>
                <tr><td colspan="2">4. Communicable disease of public health significance (e.g. TB)? <strong>{{ $student->has_communicable_disease ? 'Yes' : 'No' }}</strong></td></tr>
                <tr><td colspan="2">5. Under 18, requiring consent for enrollment? <strong>{{ $student->is_minor_requiring_consent ? 'Yes' : 'No' }}</strong></td></tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Signature</div>
        <div class="section-body">
            @if($student->signature_path && Storage::disk('public')->exists($student->signature_path))
                <div class="signature-block">
                    <img src="{{ Storage::disk('public')->path($student->signature_path) }}" alt="Signature">
                </div>
            @else
                <p>No signature on file.</p>
            @endif
        </div>
    </div>

    <div class="footer">
        Informatics College Pokhara Pvt. Ltd. &middot; Matepani-12, Pokhara, Kaski, Nepal &middot; +977 061 538115 / 522977 &middot; info@icp.edu.np &middot; icp.edu.np
        <br>Generated {{ now()->format('d M, Y H:i') }}. This is a system-generated document.
    </div>

</body>
</html>
