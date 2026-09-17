<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentQualificationDocument extends Model
{
    use HasUuids;

    protected $fillable = [
        'student_qualification_id',
        'file_path',
        'original_filename',
    ];

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(StudentQualification::class, 'student_qualification_id');
    }
}
