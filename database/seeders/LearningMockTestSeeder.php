<?php

namespace Database\Seeders;

use App\Models\LearningCategory;
use App\Models\MockTest;
use Illuminate\Database\Seeder;

class LearningMockTestSeeder extends Seeder
{
    public function run(): void
    {
        $cats = LearningCategory::pluck('id', 'slug');

        $tests = [
            // ── Driving License ──────────────────────────────────────────
            [
                'learning_category_id' => $cats['driving_license'] ?? null,
                'title'          => 'Driving License Full Mock Test',
                'title_np'       => 'ड्राइभिङ लाइसेन्स पूर्ण मोक परीक्षा',
                'description'    => 'Complete 25-question mock test covering traffic signs, road rules, vehicle categories, and emergency procedures. Matches the actual written exam format.',
                'description_np' => 'ट्राफिक चिह्न, सडक नियम, सवारी श्रेणी र आपतकालीन प्रक्रिया समेटिएको २५ प्रश्नको पूर्ण मोक परीक्षा।',
                'category'       => 'driving_license',
                'question_count' => 25,
                'duration_minutes'=> 30,
                'pass_percentage'=> 60,
                'negative_marking'=> false,
                'negative_value' => 0.25,
                'is_active'      => true,
                'is_featured'    => true,
            ],
            [
                'learning_category_id' => $cats['driving_license'] ?? null,
                'title'          => 'Driving License Quick Practice (15 Questions)',
                'title_np'       => 'ड्राइभिङ लाइसेन्स छोटो अभ्यास (१५ प्रश्न)',
                'description'    => 'Quick 15-question warm-up test. Great for daily practice.',
                'description_np' => 'दैनिक अभ्यासका लागि छोटो १५ प्रश्नको परीक्षा।',
                'category'       => 'driving_license',
                'question_count' => 15,
                'duration_minutes'=> 15,
                'pass_percentage'=> 60,
                'negative_marking'=> false,
                'negative_value' => 0.25,
                'is_active'      => true,
                'is_featured'    => false,
            ],
            // ── Loksewa ───────────────────────────────────────────────────
            [
                'learning_category_id' => $cats['loksewa'] ?? null,
                'title'          => 'Loksewa General Mock Test',
                'title_np'       => 'लोकसेवा सामान्य मोक परीक्षा',
                'description'    => 'General knowledge mock test for Loksewa exams. Covers Nepal constitution, history, geography, and current affairs.',
                'description_np' => 'लोकसेवा परीक्षाका लागि सामान्य ज्ञान मोक परीक्षा। नेपाल संविधान, इतिहास, भूगोल र समसामयिक समावेश।',
                'category'       => 'loksewa',
                'question_count' => 25,
                'duration_minutes'=> 30,
                'pass_percentage'=> 60,
                'negative_marking'=> true,
                'negative_value' => 0.25,
                'is_active'      => true,
                'is_featured'    => true,
            ],
            [
                'learning_category_id' => $cats['loksewa'] ?? null,
                'title'          => 'Loksewa Nayab Subba Level Mock',
                'title_np'       => 'लोकसेवा नायब सुब्बा स्तर मोक',
                'description'    => 'Mock test targeting Nayab Subba level exam questions with negative marking.',
                'description_np' => 'नकारात्मक अंक सहित नायब सुब्बा स्तर परीक्षाका प्रश्नहरू।',
                'category'       => 'loksewa',
                'question_count' => 50,
                'duration_minutes'=> 45,
                'pass_percentage'=> 60,
                'negative_marking'=> true,
                'negative_value' => 0.25,
                'is_active'      => true,
                'is_featured'    => false,
            ],
            // ── Finance ───────────────────────────────────────────────────
            [
                'learning_category_id' => $cats['finance'] ?? null,
                'title'          => 'Finance & Banking Basics Mock Test',
                'title_np'       => 'वित्त र बैंकिङ आधारभूत मोक परीक्षा',
                'description'    => 'Test your knowledge of banking, taxation, investment, and insurance in Nepal.',
                'description_np' => 'नेपालमा बैंकिङ, कर, लगानी र बीमाको ज्ञान जाँच्नुहोस्।',
                'category'       => 'finance',
                'question_count' => 20,
                'duration_minutes'=> 25,
                'pass_percentage'=> 60,
                'negative_marking'=> false,
                'negative_value' => 0.25,
                'is_active'      => true,
                'is_featured'    => true,
            ],
            // ── Rights & Law ──────────────────────────────────────────────
            [
                'learning_category_id' => $cats['rights'] ?? null,
                'title'          => 'Rights & Labor Law Mock Test',
                'title_np'       => 'अधिकार र श्रम कानुन मोक परीक्षा',
                'description'    => 'Know your rights! Test on fundamental rights, labor law, and consumer protection.',
                'description_np' => 'आफ्नो अधिकार जान्नुहोस्! मौलिक हक, श्रम कानुन र उपभोक्ता संरक्षणमा परीक्षा।',
                'category'       => 'rights',
                'question_count' => 20,
                'duration_minutes'=> 25,
                'pass_percentage'=> 60,
                'negative_marking'=> false,
                'negative_value' => 0.25,
                'is_active'      => true,
                'is_featured'    => false,
            ],
            // ── Competitive ───────────────────────────────────────────────
            [
                'learning_category_id' => $cats['competitive'] ?? null,
                'title'          => 'Competitive Exam General Mock',
                'title_np'       => 'प्रतिस्पर्धात्मक परीक्षा सामान्य मोक',
                'description'    => 'Mock test for banking, teaching license, and government recruitment exams.',
                'description_np' => 'बैंकिङ, शिक्षक सेवा र सरकारी भर्ती परीक्षाका लागि मोक परीक्षा।',
                'category'       => 'competitive',
                'question_count' => 25,
                'duration_minutes'=> 30,
                'pass_percentage'=> 60,
                'negative_marking'=> true,
                'negative_value' => 0.25,
                'is_active'      => true,
                'is_featured'    => true,
            ],
            [
                'learning_category_id' => $cats['competitive'] ?? null,
                'title'          => 'Banking Exam Mock Test (With Negative Marking)',
                'title_np'       => 'बैंकिङ परीक्षा मोक (नकारात्मक अंक सहित)',
                'description'    => 'Simulates actual bank recruitment exam with negative marking for wrong answers.',
                'description_np' => 'गलत उत्तरमा नकारात्मक अंक सहित वास्तविक बैंक भर्ती परीक्षाको अनुकरण।',
                'category'       => 'competitive',
                'question_count' => 30,
                'duration_minutes'=> 35,
                'pass_percentage'=> 60,
                'negative_marking'=> true,
                'negative_value' => 0.50,
                'is_active'      => true,
                'is_featured'    => false,
            ],
        ];

        foreach ($tests as $test) {
            if (!$test['learning_category_id']) continue;
            MockTest::updateOrCreate(
                ['title' => $test['title'], 'category' => $test['category']],
                $test
            );
        }

        $this->command->info('✅ LearningMockTestSeeder: ' . count($tests) . ' mock tests seeded.');
    }
}
