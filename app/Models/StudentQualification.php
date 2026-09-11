<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentQualification extends Model
{
    use HasUuids;

    protected $fillable = [
        'student_id',
        'document_type',
        'awarded_year',
        'subject',
        'institute_name',
        'score',
        'document_path',
        'sort_order',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
