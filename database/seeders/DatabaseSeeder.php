<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            CompanySettingSeeder::class,
            LeadStageSeeder::class,
            LeadSourceSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            [
                'email' => env(
                    'ADMIN_EMAIL',
                    'admin@ithub.local'
                ),
            ],
            [
                'name' => env(
                    'ADMIN_NAME',
                    'System Administrator'
                ),

                'password' => env(
                    'ADMIN_PASSWORD',
                    'ChangeMe123!ChangeMe'
                ),

                'status' => 'active',

                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }
    }
}
