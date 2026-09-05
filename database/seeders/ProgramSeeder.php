<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\LearningCategory;
use App\Models\Program;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $cats = LearningCategory::pluck('id', 'slug');
        $count = 0;

        $programs = [
            // ── Driving License ──────────────────────────────────────────
            [
                'learning_category_id' => $cats['driving_license'] ?? null,
                'slug'          => 'driving-license-nepal',
                'title_en'      => 'Driving License Preparation',
                'title_np'      => 'ड्राइभिङ लाइसेन्स तयारी',
                'description_en'=> 'Complete preparation for Nepal driving license written test and trial. Covers all vehicle categories A, B, C, D.',
                'description_np'=> 'नेपाल ड्राइभिङ लाइसेन्स लिखित परीक्षा र ट्रायलको पूर्ण तयारी।',
                'icon'          => '🚗', 'color_code' => '#2563EB',
                'is_free' => true, 'price' => 0, 'is_published' => true, 'display_order' => 1,
                'courses' => [
                    ['title_en' => 'Category A — Motorcycle',   'title_np' => 'श्रेणी क — मोटरसाइकल',   'is_published' => true, 'display_order' => 1,
                     'subjects' => [
                         ['title_en' => 'Traffic Signs & Rules',    'title_np' => 'ट्राफिक चिह्न र नियम',     'icon' => '🚦', 'color_code' => '#EF4444'],
                         ['title_en' => 'Road Safety',              'title_np' => 'सडक सुरक्षा',               'icon' => '⛑️', 'color_code' => '#F97316'],
                         ['title_en' => 'Vehicle Mechanics',        'title_np' => 'सवारी मेकानिक्स',           'icon' => '🔧', 'color_code' => '#6366F1'],
                     ]],
                    ['title_en' => 'Category B — Light Vehicle', 'title_np' => 'श्रेणी ख — हलुका सवारी', 'is_published' => true, 'display_order' => 2,
                     'subjects' => [
                         ['title_en' => 'Traffic Laws',             'title_np' => 'ट्राफिक कानुन',             'icon' => '⚖️', 'color_code' => '#8B5CF6'],
                         ['title_en' => 'Practical Trial Tips',     'title_np' => 'प्रयोगात्मक ट्रायल',       'icon' => '🏁', 'color_code' => '#10B981'],
                     ]],
                ],
            ],

            // ── Loksewa ──────────────────────────────────────────────────
            [
                'learning_category_id' => $cats['loksewa'] ?? null,
                'slug'          => 'loksewa-preparation',
                'title_en'      => 'Loksewa Preparation',
                'title_np'      => 'लोकसेवा तयारी',
                'description_en'=> 'Complete preparation for Nepal Public Service Commission. Covers Kharidar, Nayab Subba, and Section Officer levels.',
                'description_np'=> 'नेपाल लोकसेवा आयोगको पूर्ण तयारी। खरिदार, नायब सुब्बा र शाखा अधिकृत तह।',
                'icon'          => '🏛️', 'color_code' => '#7C3AED',
                'is_free' => true, 'price' => 0, 'is_published' => true, 'display_order' => 2,
                'courses' => [
                    ['title_en' => 'Kharidar Level (Non-Gazetted)',  'title_np' => 'खरिदार तह',     'is_published' => true, 'display_order' => 1,
                     'subjects' => [
                         ['title_en' => 'Nepali Language',           'title_np' => 'नेपाली भाषा',           'icon' => '🔤', 'color_code' => '#EC4899'],
                         ['title_en' => 'General Knowledge',         'title_np' => 'सामान्य ज्ञान',         'icon' => '🌍', 'color_code' => '#3B82F6'],
                         ['title_en' => 'Math & Reasoning',          'title_np' => 'गणित र तर्क',           'icon' => '🔢', 'color_code' => '#10B981'],
                         ['title_en' => 'Nepal Constitution',        'title_np' => 'नेपाल संविधान',         'icon' => '📜', 'color_code' => '#F59E0B'],
                     ]],
                    ['title_en' => 'Nayab Subba Level',             'title_np' => 'नायब सुब्बा तह', 'is_published' => true, 'display_order' => 2,
                     'subjects' => [
                         ['title_en' => 'General Knowledge',         'title_np' => 'सामान्य ज्ञान',         'icon' => '🌍', 'color_code' => '#3B82F6'],
                         ['title_en' => 'Current Affairs',           'title_np' => 'समसामयिक',               'icon' => '📰', 'color_code' => '#EF4444'],
                         ['title_en' => 'Administrative Law',        'title_np' => 'प्रशासनिक कानुन',       'icon' => '⚖️', 'color_code' => '#8B5CF6'],
                         ['title_en' => 'Office Management',         'title_np' => 'कार्यालय व्यवस्थापन',   'icon' => '🏢', 'color_code' => '#06B6D4'],
                     ]],
                    ['title_en' => 'Section Officer Level',         'title_np' => 'शाखा अधिकृत तह', 'is_published' => true, 'display_order' => 3,
                     'subjects' => [
                         ['title_en' => 'Public Administration',     'title_np' => 'सार्वजनिक प्रशासन',     'icon' => '🏛️', 'color_code' => '#7C3AED'],
                         ['title_en' => 'Development Economics',     'title_np' => 'विकास अर्थशास्त्र',     'icon' => '📊', 'color_code' => '#059669'],
                         ['title_en' => 'Management & Planning',     'title_np' => 'व्यवस्थापन र योजना',    'icon' => '📋', 'color_code' => '#D97706'],
                     ]],
                ],
            ],

            // ── Finance ──────────────────────────────────────────────────
            [
                'learning_category_id' => $cats['finance'] ?? null,
                'slug'          => 'finance-education',
                'title_en'      => 'Finance Education',
                'title_np'      => 'वित्तीय शिक्षा',
                'description_en'=> 'Personal finance, banking, investment, insurance, and taxation for Nepali citizens.',
                'description_np'=> 'नेपाली नागरिकका लागि वित्तीय साक्षरता।',
                'icon'          => '💰', 'color_code' => '#059669',
                'is_free' => true, 'price' => 0, 'is_published' => true, 'display_order' => 3,
                'courses' => [
                    ['title_en' => 'Personal Finance Basics',   'title_np' => 'व्यक्तिगत वित्त', 'is_published' => true, 'display_order' => 1,
                     'subjects' => [
                         ['title_en' => 'Banking & Accounts',       'title_np' => 'बैंकिङ र खाता',         'icon' => '🏦', 'color_code' => '#3B82F6'],
                         ['title_en' => 'Investment & Stocks',      'title_np' => 'लगानी र शेयर',           'icon' => '📈', 'color_code' => '#10B981'],
                         ['title_en' => 'Insurance',                 'title_np' => 'बीमा',                   'icon' => '🛡️', 'color_code' => '#8B5CF6'],
                         ['title_en' => 'Taxation',                  'title_np' => 'कर',                     'icon' => '🧾', 'color_code' => '#F59E0B'],
                     ]],
                ],
            ],

            // ── Rights & Law ──────────────────────────────────────────────
            [
                'learning_category_id' => $cats['rights'] ?? null,
                'slug'          => 'rights-and-law',
                'title_en'      => 'Rights & Law',
                'title_np'      => 'अधिकार र कानुन',
                'description_en'=> 'Fundamental rights, labor law, consumer protection, and legal procedures in Nepal.',
                'description_np'=> 'मौलिक हक, श्रम कानुन, उपभोक्ता संरक्षण र कानुनी प्रक्रिया।',
                'icon'          => '⚖️', 'color_code' => '#DC2626',
                'is_free' => true, 'price' => 0, 'is_published' => true, 'display_order' => 4,
                'courses' => [
                    ['title_en' => 'Citizen Rights',   'title_np' => 'नागरिक अधिकार', 'is_published' => true, 'display_order' => 1,
                     'subjects' => [
                         ['title_en' => 'Fundamental Rights',       'title_np' => 'मौलिक हक',               'icon' => '🏛️', 'color_code' => '#7C3AED'],
                         ['title_en' => 'Labor Rights',              'title_np' => 'श्रम अधिकार',            'icon' => '👷', 'color_code' => '#EF4444'],
                         ['title_en' => 'Consumer Rights',           'title_np' => 'उपभोक्ता अधिकार',       'icon' => '🛒', 'color_code' => '#F97316'],
                     ]],
                ],
            ],

            // ── Competitive ───────────────────────────────────────────────
            [
                'learning_category_id' => $cats['competitive'] ?? null,
                'slug'          => 'competitive-exams',
                'title_en'      => 'Competitive Exam Preparation',
                'title_np'      => 'प्रतिस्पर्धात्मक परीक्षा तयारी',
                'description_en'=> 'Banking exams, Teaching Service Commission, Nepal Army/Police recruitment preparation.',
                'description_np'=> 'बैंकिङ, शिक्षक सेवा आयोग, नेपाली सेना/प्रहरी भर्ती तयारी।',
                'icon'          => '📝', 'color_code' => '#D97706',
                'is_free' => true, 'price' => 0, 'is_published' => true, 'display_order' => 5,
                'courses' => [
                    ['title_en' => 'Banking Exam Preparation',     'title_np' => 'बैंकिङ परीक्षा', 'is_published' => true, 'display_order' => 1,
                     'subjects' => [
                         ['title_en' => 'Banking Knowledge',        'title_np' => 'बैंकिङ ज्ञान',          'icon' => '🏦', 'color_code' => '#3B82F6'],
                         ['title_en' => 'English & Aptitude',       'title_np' => 'अंग्रेजी र योग्यता',    'icon' => '🔡', 'color_code' => '#10B981'],
                         ['title_en' => 'Current Affairs',          'title_np' => 'समसामयिक',               'icon' => '📰', 'color_code' => '#EF4444'],
                     ]],
                    ['title_en' => 'Teaching Service Commission',   'title_np' => 'शिक्षक सेवा आयोग', 'is_published' => true, 'display_order' => 2,
                     'subjects' => [
                         ['title_en' => 'Subject Knowledge',        'title_np' => 'विषय ज्ञान',             'icon' => '📚', 'color_code' => '#8B5CF6'],
                         ['title_en' => 'Teaching Methodology',     'title_np' => 'शिक्षण विधि',           'icon' => '🎓', 'color_code' => '#EC4899'],
                         ['title_en' => 'Education Law',            'title_np' => 'शिक्षा कानुन',           'icon' => '⚖️', 'color_code' => '#F59E0B'],
                     ]],
                    ['title_en' => 'Nepal Army & Police',           'title_np' => 'सेना र प्रहरी भर्ती', 'is_published' => true, 'display_order' => 3,
                     'subjects' => [
                         ['title_en' => 'Physical Requirements',    'title_np' => 'शारीरिक आवश्यकता',     'icon' => '💪', 'color_code' => '#06B6D4'],
                         ['title_en' => 'Written Exam Prep',        'title_np' => 'लिखित परीक्षा',         'icon' => '✍️', 'color_code' => '#D97706'],
                     ]],
                ],
            ],
        ];

        foreach ($programs as $pData) {
            if (!$pData['learning_category_id']) continue;

            $courses = $pData['courses'];
            unset($pData['courses']);

            $program = Program::updateOrCreate(['slug' => $pData['slug']], $pData);
            $count++;

            foreach ($courses as $cData) {
                $subjects = $cData['subjects'] ?? [];
                unset($cData['subjects']);

                $course = Course::updateOrCreate(
                    ['program_id' => $program->id, 'title_en' => $cData['title_en']],
                    array_merge($cData, ['program_id' => $program->id])
                );

                foreach ($subjects as $sData) {
                    Subject::updateOrCreate(
                        ['course_id' => $course->id, 'title_en' => $sData['title_en']],
                        array_merge($sData, ['course_id' => $course->id, 'is_active' => true])
                    );
                }

                $course->syncCounts();
            }
        }

        $this->command->info("✅ ProgramSeeder: {$count} programs with courses and subjects seeded.");
    }
}
