<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentQualification extends Model
{
    use HasUuids;

    /** Allowed values for the `score_type` column. */
    public const SCORE_TYPES = ['GPA', 'Percentage', 'Grade', 'CGPA', 'Marks', 'Division', 'Other'];

    protected $fillable = [
        'student_id',
        'document_type',
        'awarded_year',
        'faculty',
        'institute_name',
        'score',
        'score_type',
        'qualification_description',
        'is_highest',
        'is_record',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_highest' => 'boolean',
            'is_record' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentQualificationDocument::class);
    }
}
