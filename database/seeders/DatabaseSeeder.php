<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(DocumentTypeSeeder::class);
        $this->call(FacultySeeder::class);
        $this->call(InstituteSeeder::class);

        // Seed Admin User
        User::updateOrCreate(
            ['email' => 'admin@icp.edu.np'],
            [
                'name' => 'System Administrator',
                'position' => 'Head of Admissions',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'status' => 'active',
                'image_url' => 'https://ui-avatars.com/api/?name=System+Admin&background=1e293b&color=fff',
            ]
        );

        // Seed Sample Active Staff User
        $aarav = User::updateOrCreate(
            ['email' => 'aarav@icp.edu.np'],
            [
                'name' => 'Aarav Shrestha',
                'position' => 'Senior Admissions Counselor',
                'password' => Hash::make('12345678'),
                'role' => 'staff',
                'status' => 'active',
                'image_url' => 'https://ui-avatars.com/api/?name=Aarav+Shrestha&background=0284c7&color=fff',
            ]
        );
        $aarav->syncPermissions(PermissionSeeder::defaultSlugs());

        // Seed Sample Inactive Staff User (to test inactive login restriction)
        $suman = User::updateOrCreate(
            ['email' => 'suman@icp.edu.np'],
            [
                'name' => 'Suman Gurung',
                'position' => 'Junior Officer',
                'password' => Hash::make('12345678'),
                'role' => 'staff',
                'status' => 'inactive',
                'image_url' => 'https://ui-avatars.com/api/?name=Suman+Gurung&background=64748b&color=fff',
            ]
        );
        $suman->syncPermissions(PermissionSeeder::defaultSlugs());

        Course::updateOrCreate(
            ['title' => 'BIT'],
            [
                'display_title' => 'BSc (Hons) Computing',
                'levels' => ['04', '05', '06'],
                'credits' => null,
                'description' => null,
            ]
        );

        Course::updateOrCreate(
            ['title' => 'BBA'],
            [
                'display_title' => 'BA (Hons) Business Administration',
                'levels' => ['04', '05', '06', '07'],
                'credits' => null,
                'description' => null,
            ]
        );
    }
}
