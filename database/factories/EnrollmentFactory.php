<?php

namespace Database\Factories;

use App\Models\AdmissionYear;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'admission_id' => fake()->unique()->numerify('########'),
            'admission_year_id' => AdmissionYear::factory(),
            'course_id' => Course::factory(),
            'level' => '04',
            'entry_type' => 'Standard',
            'semester' => fake()->randomElement(['Spring', 'Summer', 'Autumn']),
            'declared_date' => now()->format('Y-m-d'),
        ];
    }
}
