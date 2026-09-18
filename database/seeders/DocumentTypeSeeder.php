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
    private const DOCUMENT_TYPES = [
        'SEE' => 'SEE (Agg GPA- 4.00/ English A, Maths A)',
        'NEB' => 'NEB +2 (CGPA-4.00 Year 12 Eng- A)',
        'A-Level' => 'A-level (Agg GPA- 4.00/ English A+, Maths A+)',
    ];

    public function run(): void
    {
        foreach (self::DOCUMENT_TYPES as $name => $formatHint) {
            DocumentType::updateOrCreate(
                ['name' => $name],
                ['format_hint' => $formatHint]
            );
        }
    }
}
