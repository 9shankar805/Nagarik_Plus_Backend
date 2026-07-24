<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $news = [
            [
                'title'        => 'Passport Service Resuming Monday After Holiday',
                'title_np'     => 'सोमबारदेखि राहदानी सेवा पुनः सञ्चालन',
                'content'      => 'The Department of Passports announces that passport services will resume from this coming Monday following the national holiday period. Citizens are advised to schedule appointments in advance via dop.gov.np.',
                'content_np'   => 'राहदानी विभागले जानकारी दिएको छ कि राष्ट्रिय बिदाको अवधि पछि आगामी सोमबारदेखि राहदानी सेवा पुनः सञ्चालन हुनेछ।',
                'category'     => 'service',
                'source'       => 'Department of Passports',
                'source_url'   => 'https://dop.gov.np',
                'is_verified'  => true,
                'is_featured'  => true,
                'is_published' => true,
                'published_at' => now()->subHours(2),
            ],
            [
                'title'        => 'New Driving License Rules Effective from Baisakh 2081',
                'title_np'     => 'बैशाख २०८१ देखि नयाँ सवारी चालक अनुमतिपत्र नियम लागू',
                'content'      => 'The Department of Transport Management has announced new rules for driving license applicants effective from Baisakh 2081. All new applicants must complete a mandatory 3-hour road safety awareness course before appearing for the written examination.',
                'content_np'   => 'यातायात व्यवस्था विभागले बैशाख २०८१ देखि सवारी चालक अनुमतिपत्रका लागि नयाँ नियमहरू लागू हुने जानकारी दिएको छ।',
                'category'     => 'notice',
                'source'       => 'Department of Transport Management',
                'source_url'   => 'https://dotm.gov.np',
                'is_verified'  => true,
                'is_featured'  => false,
                'is_published' => true,
                'published_at' => now()->subDay(),
            ],
            [
                'title'        => 'PAN Card Registration Deadline Extended to Poush End',
                'title_np'     => 'प्यान कार्ड दर्ता म्याद पुस मसान्तसम्म थप',
                'content'      => 'The Inland Revenue Department has extended the deadline for mandatory PAN card registration for all employed individuals. The new deadline is Poush 30, 2081. Citizens are encouraged to register online at ird.gov.np.',
                'content_np'   => 'आन्तरिक राजश्व विभागले सबै कर्मचारीहरूको लागि अनिवार्य प्यान कार्ड दर्ताको म्याद पुस ३० गतेसम्म थप गरेको छ।',
                'category'     => 'deadline',
                'source'       => 'Inland Revenue Department',
                'source_url'   => 'https://ird.gov.np',
                'is_verified'  => true,
                'is_featured'  => false,
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'title'        => 'National ID Enrollment Centers Now Open in All 77 Districts',
                'title_np'     => '७७ वटै जिल्लामा राष्ट्रिय परिचयपत्र नामांकन केन्द्र सञ्चालन',
                'content'      => 'The government has successfully opened National ID enrollment centers in all 77 districts of Nepal. Citizens aged 16 and above can now enroll for their National Identity Card at the nearest center with their citizenship certificate.',
                'content_np'   => 'सरकारले नेपालका सबै ७७ जिल्लामा राष्ट्रिय परिचयपत्र नामांकन केन्द्रहरू सफलतापूर्वक खोलेको छ।',
                'category'     => 'service',
                'source'       => 'Department of National ID and Civil Registration',
                'source_url'   => 'https://nid.gov.np',
                'is_verified'  => true,
                'is_featured'  => true,
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title'        => 'Loksewa Written Exam Schedule Released for 2081',
                'title_np'     => 'लोक सेवा लिखित परीक्षा तालिका प्रकाशित',
                'content'      => 'The Public Service Commission has released the schedule for upcoming written examinations for 2081. Candidates are advised to download their admit cards from the official PSC website at psc.gov.np.',
                'content_np'   => 'लोक सेवा आयोगले २०८१ का लागि आगामी लिखित परीक्षाहरूको तालिका प्रकाशित गरेको छ।',
                'category'     => 'exam',
                'source'       => 'Public Service Commission',
                'source_url'   => 'https://psc.gov.np',
                'is_verified'  => true,
                'is_featured'  => false,
                'is_published' => true,
                'published_at' => now()->subDays(4),
            ],
            [
                'title'        => 'Vehicle Emission Test Deadline: Ashwin 30',
                'title_np'     => 'सवारी उत्सर्जन परीक्षण म्याद: असोज ३०',
                'content'      => 'All vehicles registered before 2075 must complete emission testing by Ashwin 30. Failure to comply will result in a fine and temporary registration suspension. Test centers are open across Kathmandu Valley.',
                'content_np'   => '२०७५ अघि दर्ता भएका सबै सवारी साधनले असोज ३० भित्र उत्सर्जन परीक्षण सम्पन्न गर्नुपर्नेछ।',
                'category'     => 'deadline',
                'source'       => 'Department of Transport Management',
                'source_url'   => 'https://dotm.gov.np',
                'is_verified'  => true,
                'is_featured'  => false,
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
        ];

        foreach ($news as $item) {
            News::create($item);
        }
    }
}
