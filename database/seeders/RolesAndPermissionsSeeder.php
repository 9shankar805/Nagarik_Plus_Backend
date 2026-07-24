<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = config('app.admin_email', 'admin@nagarikplus.com');
        $adminPassword = config('app.admin_password', 'password');

        $admin = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Super Admin',
                'phone' => '9800000000',
                'password' => Hash::make($adminPassword),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super admin created/updated.');
        $this->command->info('Email: ' . $adminEmail);
        $this->command->info('Password: ' . $adminPassword);
    }
}
