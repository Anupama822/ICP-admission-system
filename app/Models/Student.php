<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory, HasUuids;

    /** Enrollments are grouped into classes of this size: C1, C2, ... */
    private const GROUP_SIZE = 30;

    /** Allowed values for the `entry_type` column. */
    public const ENTRY_TYPES = ['Standard', 'Non-standard'];

    protected $fillable = [
        'admission_id',
        'admission_year_id',
        'course_id',
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
        'declared_date',
        'photo_path',
        'level',
        'entry_type',
        'semester',
        'group',
        'biometric_id',
        'university_registration_no',
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
            'declared_date' => 'date',
            'has_disorder' => 'boolean',
            'is_drug_abuser' => 'boolean',
            'has_criminal_record' => 'boolean',
            'has_communicable_disease' => 'boolean',
            'is_minor_requiring_consent' => 'boolean',
        ];
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

    /**
     * The next enrollment sequence number for an intake year, and the class
     * group ("C1", "C2", ...) it lands in. Both are derived from the same
     * locked read so the admission ID and the group can never drift apart.
     *
     * Scoped by the year *string* (not the admission_year_id row) so the
     * 8-digit admission ID stays unique even if two AdmissionYear rows ever
     * share the same year value.
     *
     * @return array{sequence: int, admission_id: string, group: string}
     */
    public static function nextEnrollment(string $year): array
    {
        $latest = static::where('admission_id', 'like', $year.'%')
            ->lockForUpdate()
            ->orderByDesc('admission_id')
            ->value('admission_id');

        $sequence = $latest ? ((int) substr($latest, -4)) + 1 : 1;

        return [
            'sequence' => $sequence,
            'admission_id' => sprintf('%s%04d', $year, $sequence),
            'group' => 'C'.(int) ceil($sequence / self::GROUP_SIZE),
        ];
    }

}
