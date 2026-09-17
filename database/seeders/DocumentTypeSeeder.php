<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * The starting set of educational boards offered on the student
     * enrollment form. Admins can add more from Settings > Educational
     * Boards.
     */
    private const NAMES = ['SEE', 'NEB', 'A-Level'];

    public function run(): void
    {
        foreach (self::NAMES as $name) {
            DocumentType::updateOrCreate(['name' => $name]);
        }
    }
}
