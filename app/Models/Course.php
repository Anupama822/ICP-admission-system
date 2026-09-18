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
        'display_title',
        'levels',
        'credits',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'decimal:1',
            'levels' => 'array',
        ];
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * The short, friendly name shown in listings/dropdowns/exports, falling
     * back to the full title for records created before this field existed.
     */
    public function displayTitle(): string
    {
        return $this->display_title ?: $this->title;
    }
}
