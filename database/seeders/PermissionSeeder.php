<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Fixed permission catalog, grouped by category for the staff
     * permissions screen. Admin picks from this list on the "Add Staff"
     * form (see config('staff-permissions.defaults') for what's pre-ticked)
     * and can change the grant again later from the staff member's profile.
     *
     * @var array<string, array<string, string>>
     */
    private const CATALOG = [
        'Student Records' => [
            'students.view' => 'View students',
            'students.create' => 'Create students',
            'students.edit' => 'Edit students',
            'students.delete' => 'Delete students',
            'students.export-pdf' => 'Export a student\'s enrollment PDF',
            'students.export-csv' => 'Export the student list as CSV',
        ],
        'Applications & Enquiries' => [
            'applications.view' => 'View applications & enquiries',
            'applications.manage' => 'Manage applications & enquiries',
        ],
        'Reports' => [
            'reports.view' => 'View reports',
            'reports.generate' => 'Generate reports',
        ],
    ];

    /**
     * @return array<string, array<string, string>>
     */
    public static function catalog(): array
    {
        return self::CATALOG;
    }

    /**
     * @return array<int, string>
     */
    public static function allSlugs(): array
    {
        return collect(self::CATALOG)->flatMap(fn (array $permissions) => array_keys($permissions))->values()->all();
    }

    /**
     * The permissions pre-ticked on the "Add Staff" form, as configured in
     * config/staff-permissions.php. Anything no longer in the catalog is
     * filtered out.
     *
     * @return array<int, string>
     */
    public static function defaultSlugs(): array
    {
        return array_values(array_intersect(config('staff-permissions.defaults', []), self::allSlugs()));
    }

    public function run(): void
    {
        foreach (self::allSlugs() as $slug) {
            Permission::findOrCreate($slug);
        }
    }
}
