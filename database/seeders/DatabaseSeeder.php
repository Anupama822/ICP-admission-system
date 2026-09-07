<?php

namespace Database\Seeders;

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
        User::updateOrCreate(
            ['email' => 'staff@icp.edu.np'],
            [
                'name' => 'Aarav Shrestha',
                'position' => 'Senior Admissions Counselor',
                'password' => Hash::make('12345678'),
                'role' => 'staff',
                'status' => 'active',
                'image_url' => 'https://ui-avatars.com/api/?name=Aarav+Shrestha&background=0284c7&color=fff',
            ]
        );

        // Seed Sample Inactive Staff User (to test inactive login restriction)
        User::updateOrCreate(
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
    }
}
