<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * The starting set of faculties offered on the student enrollment
     * form's Academic Qualifications rows. Admins can add more from
     * Settings > Faculties.
     */
    private const NAMES = ['Science', 'Management', 'Humanities'];

    public function run(): void
    {
        foreach (self::NAMES as $name) {
            Faculty::updateOrCreate(['name' => $name]);
        }
    }
}
