<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Update or Create admin user (password attribute automatically hashed by User model cast)
        $admin = User::updateOrCreate(
            ['email' => 'admin@nagarikplus.com'],
            [
                'name'      => 'Admin',
                'phone'     => '9800000000',
                'password'  => 'Admin@123',
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        $this->command->info("Admin user updated successfully: {$admin->email}");
    }
}
