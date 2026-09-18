<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory, HasUuids;

    /** Allowed values for the `entry_type` column on enrollments. */
    public const ENTRY_TYPES = ['Standard', 'Non-standard'];

    protected $fillable = [
        'admission_year_id',
        'course_id',
        'group',
        'biometric_id',
        'university_registration_no',
        'first_name',
        'middle_name',
        'last_name',
        'certificate_name',
        'gender',
        'dob_bs',
        'dob_ad',
        'citizenship_number',
        'citizenship_issued_date',
        'passport_number',
        'passport_issued_date',
        'photo_path',
        'permanent_address',
        'corresponding_address',
        'mobile',
        'email_1',
        'email_2',
        'father_full_name',
        'father_mobile',
        'father_email',
        'mother_full_name',
        'mother_mobile',
        'mother_email',
        'guardian_full_name',
        'guardian_relationship',
        'guardian_contact',
        'guardian_email',
        'has_disorder',
        'is_drug_abuser',
        'has_criminal_record',
        'has_communicable_disease',
        'is_minor_requiring_consent',
        'signature_path',
    ];

    protected function casts(): array
    {
        return [
            'dob_ad' => 'date',
            // citizenship_issued_date / passport_issued_date are recorded in
            // the Bikram Sambat (BS) calendar, like dob_bs, so they stay
            // plain strings rather than Carbon-cast Gregorian dates.
            'has_disorder' => 'boolean',
            'is_drug_abuser' => 'boolean',
            'has_criminal_record' => 'boolean',
            'has_communicable_disease' => 'boolean',
            'is_minor_requiring_consent' => 'boolean',
        ];
    }

    /**
     * The student's enrollment: admission ID, level, entry type, semester,
     * declared date, etc. A student has one today, but the relationship
     * (rather than flat columns on this table) leaves room for a later
     * re-admission to add a second one without disturbing personal details
     * already on file.
     *
     * Course and intake year are duplicated onto this table directly (see
     * course()/intake() below) so the student's current ones are available
     * without joining through here.
     */
    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class);
    }

    public function intake(): BelongsTo
    {
        return $this->belongsTo(AdmissionYear::class, 'admission_year_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(StudentQualification::class)->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function fullName(): string
    {
        return trim(collect([$this->first_name, $this->middle_name, $this->last_name])->filter()->implode(' '));
    }
}
