<?php

namespace App\Http\Requests;

use App\Models\DocumentType;
use App\Models\Faculty;
use App\Models\Institute;
use App\Models\Student;
use App\Models\StudentQualification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ability = $this->isMethod('post') ? 'students.create' : 'students.edit';

        return $this->user()?->can($ability) ?? false;
    }

    public function rules(): array
    {
        // On update the route model is bound as {student}; ignore it so the
        // record does not clash with its own identity documents.
        $ignoreId = $this->route('student')?->getKey();

        return [
            // 'admission_year_id' => ['required', 'uuid', 'exists:admission_years,id'],
            'course_id' => ['required', 'uuid', 'exists:courses,id'],

            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'dob_bs' => ['nullable', 'string', 'max:50'],
            'dob_ad' => ['required', 'date'],
            'citizenship_number' => ['nullable', 'string', 'max:50', 'regex:/^\d{2}-\d{2}-\d{2}-\d{5}$/', Rule::unique('students', 'citizenship_number')->ignore($ignoreId)],
            'citizenship_issued_date' => ['nullable', 'string', 'max:50'],
            'passport_number' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z]{2}[0-9]{7}$/', Rule::unique('students', 'passport_number')->ignore($ignoreId)],
            'passport_issued_date' => ['nullable', 'string', 'max:50'],
            // 'declared_date' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,webp', 'max:2048'],

            'level' => ['required', 'string', 'max:50'],
            'entry_type' => ['required', Rule::in(Student::ENTRY_TYPES)],
            // 'biometric_id' => ['nullable', 'string', 'max:100'],
            // 'university_registration_no' => ['nullable', 'string', 'max:100'],

            'permanent_address' => ['required', 'string', 'max:255'],
            'corresponding_address' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email_1' => ['nullable', 'email', 'max:255'],
            'email_2' => ['nullable', 'email', 'max:255'],

            'father_full_name' => ['nullable', 'string', 'max:255'],
            'father_mobile' => ['nullable', 'string', 'max:20'],
            'father_email' => ['nullable', 'email', 'max:255'],
            'mother_full_name' => ['nullable', 'string', 'max:255'],
            'mother_mobile' => ['nullable', 'string', 'max:20'],
            'mother_email' => ['nullable', 'email', 'max:255'],
            'guardian_full_name' => ['nullable', 'string', 'max:255'],
            'guardian_relationship' => ['nullable', 'string', 'max:100'],
            'guardian_contact' => ['nullable', 'string', 'max:20'],
            'guardian_email' => ['nullable', 'email', 'max:255'],

            'has_disorder' => ['required', 'boolean'],
            'is_drug_abuser' => ['required', 'boolean'],
            'has_criminal_record' => ['required', 'boolean'],
            'has_communicable_disease' => ['required', 'boolean'],
            'is_minor_requiring_consent' => ['required', 'boolean'],

            // 'signature' => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'starts_with:data:image/'],
            'signature' => ['nullable', 'string', 'starts_with:data:image/'],

            'highest_qualification' => ['required', 'array'],
            'highest_qualification.document_type' => ['required', 'string', Rule::in(DocumentType::pluck('name'))],
            'highest_qualification.awarded_year' => ['nullable', 'digits:4', 'integer'],
            'highest_qualification.faculty' => ['nullable', 'string', 'max:255'],
            'highest_qualification.institute_name' => ['required', 'string', Rule::in(Institute::pluck('name'))],
            'highest_qualification.score' => ['nullable', 'string', 'max:50'],
            'highest_qualification.score_type' => ['nullable', 'string', Rule::in(StudentQualification::SCORE_TYPES)],
            'highest_qualification.qualification_description' => ['nullable', 'string', 'max:2000'],
            'highest_qualification.existing_documents' => ['nullable', 'array'],
            'highest_qualification.existing_documents.*' => ['string'],
            'highest_qualification.documents' => ['nullable', 'array'],
            'highest_qualification.documents.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'qualifications' => ['nullable', 'array'],
            'qualifications.*.document_type' => ['required', 'string', Rule::in(DocumentType::pluck('name'))],
            'qualifications.*.awarded_year' => ['nullable', 'digits:4', 'integer'],
            'qualifications.*.faculty' => ['nullable', 'string', 'max:255'],
            'qualifications.*.institute_name' => ['nullable', 'string', 'max:255'],
            'qualifications.*.score' => ['nullable', 'string', 'max:50'],
            'qualifications.*.score_type' => ['nullable', 'string', Rule::in(StudentQualification::SCORE_TYPES)],
            'qualifications.*.qualification_description' => ['nullable', 'string', 'max:2000'],
            'qualifications.*.existing_documents' => ['nullable', 'array'],
            'qualifications.*.existing_documents.*' => ['string'],
            'qualifications.*.documents' => ['nullable', 'array'],
            'qualifications.*.documents.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            // Document Type is fixed to "Academic" server-side regardless of
            // what's submitted, so it isn't validated here.
            'academic_records' => ['nullable', 'array'],
            'academic_records.*.awarded_year' => ['nullable', 'digits:4', 'integer'],
            'academic_records.*.faculty' => ['required', 'string', Rule::in(Faculty::pluck('name'))],
            'academic_records.*.institute_name' => ['required', 'string', Rule::in(Institute::pluck('name'))],
            'academic_records.*.score' => ['nullable', 'string', 'max:50'],

            'documents' => ['nullable', 'array'],
            'documents.*.title' => ['required', 'string', 'max:100'],
            'documents.*.files' => ['nullable', 'array'],
            'documents.*.files.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('citizenship_number') && ! $this->filled('passport_number')) {
                $validator->errors()->add('citizenship_number', 'Either a citizenship number or a passport number is required.');
            }

            $parentalFields = [
                'father_full_name', 'father_mobile', 'father_email',
                'mother_full_name', 'mother_mobile', 'mother_email',
                'guardian_full_name', 'guardian_relationship', 'guardian_contact', 'guardian_email',
            ];

            if (collect($parentalFields)->every(fn (string $field) => ! $this->filled($field))) {
                $validator->errors()->add('father_full_name', 'Provide at least one parent or guardian\'s information.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'admission_year_id' => 'intake / admission year',
            'course_id' => 'course',
            'email_1' => 'email address',
            'email_2' => 'secondary email address',
            'highest_qualification.document_type' => 'highest qualification educational board',
            'highest_qualification.institute_name' => 'highest qualification awarding body',
        ];
    }

    public function messages(): array
    {
        return [
            'citizenship_number.regex' => 'Enter the citizenship number in the standard format, e.g. 12-34-56-78901.',
            'passport_number.regex' => 'Enter the passport number in the standard format, e.g. PA1234567.',
        ];
    }
}
