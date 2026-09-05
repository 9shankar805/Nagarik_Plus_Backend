<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // ── Streak badges ─────────────────────────────────────────────
            ['slug'=>'streak_3','title_en'=>'3-Day Streak!','title_np'=>'३ दिन लगातार!','description_en'=>'Study for 3 consecutive days.','description_np'=>'३ दिन लगातार अध्ययन गर्नुभयो।','icon'=>'🔥','badge_color'=>'#F97316','type'=>'streak','threshold'=>3],
            ['slug'=>'streak_7','title_en'=>'Week Warrior','title_np'=>'साप्ताहिक योद्धा','description_en'=>'Study for 7 consecutive days.','description_np'=>'७ दिन लगातार अध्ययन गर्नुभयो।','icon'=>'⚡','badge_color'=>'#EAB308','type'=>'streak','threshold'=>7],
            ['slug'=>'streak_14','title_en'=>'2-Week Champion','title_np'=>'२ हप्ते च्याम्पियन','description_en'=>'Study for 14 consecutive days.','description_np'=>'१४ दिन लगातार।','icon'=>'🏆','badge_color'=>'#F59E0B','type'=>'streak','threshold'=>14],
            ['slug'=>'streak_30','title_en'=>'Monthly Master','title_np'=>'मासिक मास्टर','description_en'=>'Study for 30 consecutive days!','description_np'=>'३० दिन लगातार अध्ययन!','icon'=>'👑','badge_color'=>'#D97706','type'=>'streak','threshold'=>30],

            // ── Daily Quiz badges ─────────────────────────────────────────
            ['slug'=>'daily_first','title_en'=>'First Daily Quiz','title_np'=>'पहिलो दैनिक प्रश्न','description_en'=>'Answer your first daily quiz.','description_np'=>'पहिलो दैनिक प्रश्नको जवाफ दिनुभयो।','icon'=>'📅','badge_color'=>'#6366F1','type'=>'daily','threshold'=>1],
            ['slug'=>'daily_7','title_en'=>'Quiz Habit','title_np'=>'प्रश्न बानी','description_en'=>'Answer daily quiz for 7 days.','description_np'=>'७ दिन दैनिक प्रश्नको जवाफ दिनुभयो।','icon'=>'📆','badge_color'=>'#8B5CF6','type'=>'daily','threshold'=>7],
            ['slug'=>'daily_30','title_en'=>'Quiz Devotee','title_np'=>'प्रश्न समर्पित','description_en'=>'Answer daily quiz for 30 days.','description_np'=>'३० दिन दैनिक प्रश्नको जवाफ दिनुभयो।','icon'=>'🌟','badge_color'=>'#7C3AED','type'=>'daily','threshold'=>30],

            // ── Quiz/Practice badges ──────────────────────────────────────
            ['slug'=>'quiz_10','title_en'=>'First 10 Questions','title_np'=>'पहिलो १० प्रश्न','description_en'=>'Answer 10 quiz questions.','description_np'=>'१० प्रश्नको जवाफ दिनुभयो।','icon'=>'📝','badge_color'=>'#3B82F6','type'=>'quiz','threshold'=>10],
            ['slug'=>'quiz_50','title_en'=>'50 Questions Done','title_np'=>'५० प्रश्न सम्पन्न','description_en'=>'Answer 50 quiz questions.','description_np'=>'५० प्रश्नको जवाफ दिनुभयो।','icon'=>'💪','badge_color'=>'#2563EB','type'=>'quiz','threshold'=>50],
            ['slug'=>'quiz_100','title_en'=>'Century!','title_np'=>'शतक!','description_en'=>'Answer 100 quiz questions.','description_np'=>'१०० प्रश्नको जवाफ दिनुभयो।','icon'=>'💯','badge_color'=>'#1D4ED8','type'=>'quiz','threshold'=>100],
            ['slug'=>'quiz_500','title_en'=>'Quiz Machine','title_np'=>'प्रश्न मेसिन','description_en'=>'Answer 500 quiz questions.','description_np'=>'५०० प्रश्नको जवाफ दिनुभयो।','icon'=>'🤖','badge_color'=>'#1E40AF','type'=>'quiz','threshold'=>500],

            // ── Test passing badges ───────────────────────────────────────
            ['slug'=>'test_pass_1','title_en'=>'First Pass!','title_np'=>'पहिलो उत्तीर्ण!','description_en'=>'Pass your first mock test.','description_np'=>'पहिलो मोक परीक्षा उत्तीर्ण।','icon'=>'✅','badge_color'=>'#10B981','type'=>'test','threshold'=>1],
            ['slug'=>'test_pass_5','title_en'=>'Pass Master','title_np'=>'पास मास्टर','description_en'=>'Pass 5 mock tests.','description_np'=>'५ वटा मोक परीक्षा उत्तीर्ण।','icon'=>'🎯','badge_color'=>'#059669','type'=>'test','threshold'=>5],
            ['slug'=>'test_pass_20','title_en'=>'Exam Ready','title_np'=>'परीक्षा तयार','description_en'=>'Pass 20 mock tests.','description_np'=>'२० वटा मोक परीक्षा उत्तीर्ण।','icon'=>'🏅','badge_color'=>'#047857','type'=>'test','threshold'=>20],

            // ── Chapter/Study badges ──────────────────────────────────────
            ['slug'=>'chapter_5','title_en'=>'Studious','title_np'=>'मेहनती','description_en'=>'Read 5 study chapters.','description_np'=>'५ वटा अध्याय पढ्नुभयो।','icon'=>'📖','badge_color'=>'#EC4899','type'=>'chapter','threshold'=>5],
            ['slug'=>'chapter_20','title_en'=>'Knowledge Seeker','title_np'=>'ज्ञान खोजकर्ता','description_en'=>'Read 20 chapters.','description_np'=>'२० वटा अध्याय पढ्नुभयो।','icon'=>'📚','badge_color'=>'#DB2777','type'=>'chapter','threshold'=>20],

            // ── Competition badges ────────────────────────────────────────
            ['slug'=>'competition_1','title_en'=>'First Competition','title_np'=>'पहिलो प्रतिस्पर्धा','description_en'=>'Enter your first competition.','description_np'=>'पहिलो प्रतिस्पर्धामा सहभागी।','icon'=>'🏟️','badge_color'=>'#EF4444','type'=>'competition','threshold'=>1],
            ['slug'=>'competition_5','title_en'=>'Competitor','title_np'=>'प्रतिस्पर्धी','description_en'=>'Enter 5 competitions.','description_np'=>'५ वटा प्रतिस्पर्धामा सहभागी।','icon'=>'⚔️','badge_color'=>'#DC2626','type'=>'competition','threshold'=>5],

            // ── Special badges ────────────────────────────────────────────
            ['slug'=>'early_bird','title_en'=>'Early Bird','title_np'=>'अग्रगामी','description_en'=>'One of our first 100 learners!','description_np'=>'हाम्रा पहिलो १०० शिक्षार्थीमध्ये एक!','icon'=>'🐦','badge_color'=>'#06B6D4','type'=>'special','threshold'=>1],
        ];

        foreach ($badges as $badge) {
            Achievement::updateOrCreate(['slug' => $badge['slug']], array_merge($badge, ['is_active' => true]));
        }

        $this->command->info('✅ AchievementSeeder: ' . count($badges) . ' badges seeded.');
    }
}
