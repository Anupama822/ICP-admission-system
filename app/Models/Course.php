<?php

namespace App\Models;

use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'credits',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'decimal:1',
        ];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
