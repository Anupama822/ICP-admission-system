<?php

namespace App\Http\Requests;

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
            'admission_year_id' => ['required', 'uuid', 'exists:admission_years,id'],
            'course_id' => ['required', 'uuid', 'exists:courses,id'],

            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'certificate_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'dob_bs' => ['nullable', 'string', 'max:50'],
            'dob_ad' => ['required', 'date'],
            'citizenship_number' => ['nullable', 'string', 'max:50', Rule::unique('students', 'citizenship_number')->ignore($ignoreId)],
            'citizenship_issued_date' => ['nullable', 'date'],
            'passport_number' => ['nullable', 'string', 'max:50', Rule::unique('students', 'passport_number')->ignore($ignoreId)],
            'passport_issued_date' => ['nullable', 'date'],
            'declared_date' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:2048'],

            'level' => ['required', 'string', 'max:50'],
            'entry_type' => ['required', 'string', 'max:100'],
            'semester' => ['required', Rule::in(['Spring', 'Summer', 'Autumn'])],
            'biometric_id' => ['nullable', 'string', 'max:100'],
            'university_registration_no' => ['nullable', 'string', 'max:100'],

            'permanent_address' => ['required', 'string', 'max:255'],
            'corresponding_address' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'email_1' => ['required', 'email', 'max:255'],
            'email_2' => ['nullable', 'email', 'max:255'],

            'father_full_name' => ['required', 'string', 'max:255'],
            'father_mobile' => ['required', 'string', 'max:20'],
            'father_email' => ['nullable', 'email', 'max:255'],
            'mother_full_name' => ['required', 'string', 'max:255'],
            'mother_mobile' => ['required', 'string', 'max:20'],
            'mother_email' => ['nullable', 'email', 'max:255'],
            'guardian_full_name' => ['nullable', 'string', 'max:255'],
            'guardian_contact' => ['nullable', 'string', 'max:20'],
            'guardian_email' => ['nullable', 'email', 'max:255'],

            'highest_qualification' => ['required', 'string', 'max:255'],
            'awarding_body' => ['required', 'string', 'max:255'],
            'qualification_description' => ['nullable', 'string', 'max:2000'],

            'has_disorder' => ['required', 'boolean'],
            'is_drug_abuser' => ['required', 'boolean'],
            'has_criminal_record' => ['required', 'boolean'],
            'has_communicable_disease' => ['required', 'boolean'],
            'is_minor_requiring_consent' => ['required', 'boolean'],

            'signature' => [$this->isMethod('post') ? 'required' : 'nullable', 'string', 'starts_with:data:image/'],

            'qualifications' => ['nullable', 'array'],
            'qualifications.*.document_type' => ['required', 'string', 'max:100'],
            'qualifications.*.awarded_year' => ['nullable', 'digits:4', 'integer'],
            'qualifications.*.subject' => ['required', 'string', 'max:255'],
            'qualifications.*.institute_name' => ['required', 'string', 'max:255'],
            'qualifications.*.score' => ['nullable', 'string', 'max:50'],
            'qualifications.*.document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],

            'documents' => ['nullable', 'array'],
            'documents.*.title' => ['required', 'string', 'max:100'],
            'documents.*.file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('citizenship_number') && ! $this->filled('passport_number')) {
                $validator->errors()->add('citizenship_number', 'Either a citizenship number or a passport number is required.');
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
        ];
    }
}
