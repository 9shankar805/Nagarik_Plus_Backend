<?php

namespace Database\Seeders;

use App\Models\Advisor;
use App\Models\AdvisorCategory;
use Illuminate\Database\Seeder;

class AdvisorSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = [
            [
                'name' => 'Legal Advisor',
                'name_np' => 'कानूनी सलाहकार',
                'description' => 'Legal services and advice',
                'icon' => '⚖️',
                'color' => '#2563eb',
            ],
            [
                'name' => 'Tax Consultant',
                'name_np' => 'कर परामर्शदाता',
                'description' => 'Tax planning and filing',
                'icon' => '📊',
                'color' => '#16a34a',
            ],
            [
                'name' => 'Passport Agent',
                'name_np' => 'पासपोर्ट एजेन्ट',
                'description' => 'Passport and visa assistance',
                'icon' => '🛂',
                'color' => '#dc2626',
            ],
            [
                'name' => 'Financial Advisor',
                'name_np' => 'वित्तीय सलाहकार',
                'description' => 'Investment and financial planning',
                'icon' => '💰',
                'color' => '#f59e0b',
            ],
        ];

        foreach ($categories as $index => $catData) {
            $catData['sort_order'] = $index + 1;
            $catData['is_active'] = true;
            $category = AdvisorCategory::create($catData);

            // Create advisors for this category
            $advisorBaseNumber = 9801000000 + ($index * 100);
            $advisors = [
                [
                    'name' => "{$catData['name']} Advisor 1",
                    'name_np' => "{$catData['name_np']} सलाहकार १",
                    'email' => "advisor.{$index}.1@example.com",
                    'phone' => (string) ($advisorBaseNumber + 1),
                    'specialization' => $catData['name'],
                    'bio' => '10+ years experience in ' . strtolower($catData['name']),
                    'consultation_fee' => 500 + ($index * 100),
                    'is_verified' => true,
                    'is_online' => true,
                    'is_available' => true,
                    'rating' => 4.8,
                    'total_reviews' => 42,
                ],
                [
                    'name' => "{$catData['name']} Advisor 2",
                    'name_np' => "{$catData['name_np']} सलाहकार २",
                    'email' => "advisor.{$index}.2@example.com",
                    'phone' => (string) ($advisorBaseNumber + 2),
                    'specialization' => $catData['name'],
                    'bio' => 'Specialist in ' . strtolower($catData['name']),
                    'consultation_fee' => 400 + ($index * 100),
                    'is_verified' => true,
                    'is_online' => false,
                    'is_available' => true,
                    'rating' => 4.5,
                    'total_reviews' => 35,
                ],
            ];

            foreach ($advisors as $advisorData) {
                $advisorData['category_id'] = $category->id;
                Advisor::create($advisorData);
            }
        }
    }
}
