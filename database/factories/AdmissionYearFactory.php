<?php

namespace Database\Factories;

use App\Models\AdmissionYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdmissionYear>
 */
class AdmissionYearFactory extends Factory
{
    protected $model = AdmissionYear::class;

    public function definition(): array
    {
        $year = fake()->unique()->numberBetween(2000, 2100);

        return [
            'title' => $year.'/'.substr((string) ($year + 1), -2),
            'year' => (string) $year,
            'is_active' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => true]);
    }
}
