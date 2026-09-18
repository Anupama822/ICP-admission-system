<?php

namespace App\Models;

use Database\Factories\AdmissionYearFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmissionYear extends Model
{
    /** @use HasFactory<AdmissionYearFactory> */
    use HasFactory, HasUuids;

    /** Allowed values for the `intake` column. */
    public const INTAKES = ['Spring', 'Autumn'];

    protected $fillable = [
        'title',
        'is_active',
        'year',
        'intake',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'admission_year_id');
    }

    /**
     * Make this the one and only active admission year.
     */
    public function activate(): void
    {
        static::where('is_active', true)
            ->whereKeyNot($this->getKey())
            ->update(['is_active' => false]);

        $this->forceFill(['is_active' => true])->save();
    }
}
