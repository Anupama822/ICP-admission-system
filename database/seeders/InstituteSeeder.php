<?php

namespace Database\Seeders;

use App\Models\Institute;
use Illuminate\Database\Seeder;

class InstituteSeeder extends Seeder
{
    /**
     * A starting institute so the "Awarding Body" / "Institute Name" fields
     * aren't empty out of the box. Admins can add more from Settings >
     * Institutes.
     */
    private const NAMES = ['Informatics College Pokhara'];

    public function run(): void
    {
        foreach (self::NAMES as $name) {
            Institute::updateOrCreate(['name' => $name]);
        }
    }
}
