<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            CitizenServiceSeeder::class,
            EmergencyContactSeeder::class,
            OfficeSeeder::class,
            NewsSeeder::class,
            QuizQuestionSeeder::class,
            RoadSignSeeder::class,
            BannerSeeder::class,
            AdvisorSeeder::class,
        ]);
    }
}
