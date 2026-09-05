<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_DEFAULT_EMAIL', 'admin@nagarikplus.com');
        $adminPassword = env('ADMIN_DEFAULT_PASSWORD', 'Admin@123');

        $admin = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Super Admin',
                'phone' => '9800000000',
                'password' => $adminPassword,
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super admin created/updated.');
        $this->command->info('Email: ' . $adminEmail);
        $this->command->info('Password: ' . $adminPassword);
    }
}
