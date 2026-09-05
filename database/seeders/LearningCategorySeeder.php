<?php

namespace Database\Seeders;

use App\Models\LearningCategory;
use Illuminate\Database\Seeder;

class LearningCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug'           => 'driving_license',
                'name_en'        => 'Driving License',
                'name_np'        => 'ड्राइभिङ लाइसेन्स',
                'description_en' => 'Complete preparation for Nepal driving license written test and trial. Covers traffic signs, road rules, vehicle regulations, and practical trial tips.',
                'description_np' => 'नेपाल ड्राइभिङ लाइसेन्स लिखित परीक्षा र ट्रायलको पूर्ण तयारी। ट्राफिक चिह्न, सडक नियम, सवारी विनियम र प्रयोगात्मक ट्रायल सुझाव समावेश।',
                'icon'           => '🚗',
                'color_code'     => '#2563EB',
                'display_order'  => 1,
                'is_active'      => true,
            ],
            [
                'slug'           => 'loksewa',
                'name_en'        => 'Loksewa (Civil Service)',
                'name_np'        => 'लोकसेवा आयोग',
                'description_en' => 'Preparation material for Nepal Public Service Commission (Loksewa) exams. Covers general knowledge, current affairs, Nepali, math, and subject-specific topics.',
                'description_np' => 'नेपाल लोकसेवा आयोग परीक्षाको तयारी सामग्री। सामान्य ज्ञान, समसामयिक, नेपाली, गणित र विषयगत विषयहरू समावेश।',
                'icon'           => '🏛️',
                'color_code'     => '#7C3AED',
                'display_order'  => 2,
                'is_active'      => true,
            ],
            [
                'slug'           => 'finance',
                'name_en'        => 'Finance Education',
                'name_np'        => 'वित्तीय शिक्षा',
                'description_en' => 'Personal finance, banking, investment, taxation, and financial literacy for Nepali citizens. Learn about savings, loans, insurance, and wealth management.',
                'description_np' => 'नेपाली नागरिकका लागि व्यक्तिगत वित्त, बैंकिङ, लगानी, कर र वित्तीय साक्षरता। बचत, ऋण, बीमा र सम्पत्ति व्यवस्थापनबारे जान्नुहोस्।',
                'icon'           => '💰',
                'color_code'     => '#059669',
                'display_order'  => 3,
                'is_active'      => true,
            ],
            [
                'slug'           => 'rights',
                'name_en'        => 'Rights & Law',
                'name_np'        => 'अधिकार र कानुन',
                'description_en' => 'Know your fundamental rights, citizen rights, labor laws, consumer rights, property law, and legal procedures in Nepal.',
                'description_np' => 'आफ्ना मौलिक अधिकार, नागरिक अधिकार, श्रम कानुन, उपभोक्ता अधिकार, सम्पत्ति कानुन र नेपालमा कानुनी प्रक्रियाबारे जान्नुहोस्।',
                'icon'           => '⚖️',
                'color_code'     => '#DC2626',
                'display_order'  => 4,
                'is_active'      => true,
            ],
            [
                'slug'           => 'competitive',
                'name_en'        => 'Other Competitive Exams',
                'name_np'        => 'अन्य प्रतिस्पर्धात्मक परीक्षा',
                'description_en' => 'Preparation for banking exams, teaching license (Shikshak Sewa), army/police recruitment, and other government competitive examinations in Nepal.',
                'description_np' => 'बैंकिङ परीक्षा, शिक्षक सेवा, सेना/प्रहरी भर्ती र नेपालका अन्य सरकारी प्रतिस्पर्धात्मक परीक्षाहरूको तयारी।',
                'icon'           => '📝',
                'color_code'     => '#D97706',
                'display_order'  => 5,
                'is_active'      => true,
            ],
        ];

        foreach ($categories as $data) {
            LearningCategory::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info('✅ LearningCategorySeeder: 5 categories seeded successfully.');
    }
}
