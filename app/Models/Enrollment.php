<?php

namespace App\Models;

use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    /** @use HasFactory<EnrollmentFactory> */
    use HasFactory, HasUuids;

    /** Enrollments are grouped into classes of this size: C1, C2, ... */
    private const GROUP_SIZE = 30;

    protected $fillable = [
        'student_id',
        'admission_id',
        'admission_year_id',
        'course_id',
        'level',
        'entry_type',
        'semester',
        'declared_date',
    ];

    protected function casts(): array
    {
        return [
            'declared_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function intake(): BelongsTo
    {
        return $this->belongsTo(AdmissionYear::class, 'admission_year_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
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
