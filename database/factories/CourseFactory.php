<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'display_title' => fake()->unique()->lexify('???'),
            'levels' => ['04', '05', '06'],
            'credits' => fake()->randomElement([1, 1.5, 2, 3, 4]),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
