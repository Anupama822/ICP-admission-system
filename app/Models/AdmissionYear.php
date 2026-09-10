<?php

namespace App\Models;

use Database\Factories\AdmissionYearFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionYear extends Model
{
    /** @use HasFactory<AdmissionYearFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'is_active',
        'year',
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
