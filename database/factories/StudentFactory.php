<?php

namespace Database\Factories;

use App\Models\AdmissionYear;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'admission_id' => fake()->unique()->numerify('########'),
            'admission_year_id' => AdmissionYear::factory(),
            'course_id' => Course::factory(),
            'first_name' => fake()->firstName(),
            'middle_name' => null,
            'last_name' => fake()->lastName(),
            'certificate_name' => fn (array $attributes) => $attributes['first_name'].' '.$attributes['last_name'],
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'dob_bs' => null,
            'dob_ad' => fake()->dateTimeBetween('-25 years', '-17 years')->format('Y-m-d'),
            'citizenship_number' => fake()->unique()->numerify('##-##-##-#####'),
            'citizenship_issued_date' => null,
            'passport_number' => null,
            'passport_issued_date' => null,
            'declared_date' => now()->format('Y-m-d'),
            'photo_path' => null,
            'level' => '04',
            'entry_type' => 'Standard',
            'semester' => fake()->randomElement(['Spring', 'Summer', 'Autumn']),
            'group' => 'C1',
            'biometric_id' => null,
            'university_registration_no' => null,
            'permanent_address' => fake()->address(),
            'corresponding_address' => fake()->address(),
            'mobile' => fake()->numerify('98########'),
            'email_1' => fake()->unique()->safeEmail(),
            'email_2' => null,
            'father_full_name' => fake()->name('male'),
            'father_mobile' => fake()->numerify('98########'),
            'father_email' => null,
            'mother_full_name' => fake()->name('female'),
            'mother_mobile' => fake()->numerify('98########'),
            'mother_email' => null,
            'guardian_full_name' => null,
            'guardian_relationship' => null,
            'guardian_contact' => null,
            'guardian_email' => null,
            'has_disorder' => false,
            'is_drug_abuser' => false,
            'has_criminal_record' => false,
            'has_communicable_disease' => false,
            'is_minor_requiring_consent' => false,
            'signature_path' => null,
        ];
    }
}
