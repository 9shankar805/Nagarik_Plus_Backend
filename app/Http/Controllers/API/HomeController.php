<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\SocialService;
use App\Models\VitalEvent;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function banners(): JsonResponse
    {
        $dbBanners = Banner::where('is_active', true)->latest()->get();

        if ($dbBanners->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $dbBanners]);
        }

        $fallbackBanners = [
            [
                'id' => 1,
                'title_en' => 'File Police Reports Easily via Nagarik App.',
                'title_np' => 'प्रहरी रिपोर्ट अब नागरिक एपबाट सजिलै।',
                'subtitle_en' => 'Anywhere, anytime – secure, fast & reliable.',
                'subtitle_np' => 'जहाँ पनि, जतिबेला पनि – सुरक्षित, छिटो र भरपर्दो।',
                'cta1' => 'File Report →',
                'cta2' => 'Learn More',
            ],
            [
                'id' => 2,
                'title_en' => 'Digital Voter ID & PAN Verification',
                'title_np' => 'डिजिटल मतदाता परिचयपत्र र प्यान प्रमाणीकरण',
                'subtitle_en' => 'Store and verify official documents in Digital Locker.',
                'subtitle_np' => 'डिजिटल लकरमा आफ्ना सरकारी कागजातहरू सुरक्षित राख्नुहोस्।',
                'cta1' => 'View Locker →',
                'cta2' => 'Add Document',
            ],
        ];

        return response()->json(['success' => true, 'data' => $fallbackBanners]);
    }

    public function socialServices(): JsonResponse
    {
        $dbServices = SocialService::where('is_active', true)->latest()->get();

        if ($dbServices->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $dbServices]);
        }

        $fallbackServices = [
            [
                'id' => 'cit',
                'title' => 'Citizenship Information',
                'title_np' => 'नागरिकता जानकारी',
                'subtitle' => 'Apply for Nepali citizenship',
                'subtitle_np' => 'नेपाली नागरिकताको लागि आवेदन गर्नुहोस्',
                'icon' => 'badge',
                'color' => '#4A5D4A',
                'image_url' => null,
            ],
            [
                'id' => 'provident-fund',
                'title' => 'Provident Fund',
                'title_np' => 'भविष्य निधि',
                'subtitle' => 'Check your balance and contributions',
                'subtitle_np' => 'तपाईंको ब्यालेन्स र योगदान जाँच गर्नुहोस्',
                'icon' => 'account_balance',
                'color' => '#2E7D32',
                'image_url' => null,
            ],
            [
                'id' => 'ssf',
                'title' => 'Social Security Fund',
                'title_np' => 'सामाजिक सुरक्षा कोष',
                'subtitle' => 'Government social security',
                'subtitle_np' => 'सरकारी सामाजिक सुरक्षा',
                'icon' => 'health_and_safety',
                'color' => '#4A5D4A',
                'image_url' => null,
            ],
        ];

        return response()->json(['success' => true, 'data' => $fallbackServices]);
    }

    public function vitalEvents(): JsonResponse
    {
        $dbEvents = VitalEvent::where('is_active', true)->latest()->get();

        if ($dbEvents->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $dbEvents]);
        }

        $fallbackEvents = [
            [
                'id' => 'birth-certificate',
                'title' => 'Birth Certificate',
                'title_np' => 'जन्म प्रमाणपत्र',
                'image_url' => null,
                'bg_color' => '#E8F5E9',
            ],
            [
                'id' => 'marriage-certificate',
                'title' => 'Marriage Certificate',
                'title_np' => 'विवाह प्रमाणपत्र',
                'image_url' => null,
                'bg_color' => '#FFE4EC',
            ],
            [
                'id' => 'death-certificate',
                'title' => 'Death Certificate',
                'title_np' => 'मृत्यु प्रमाणपत्र',
                'image_url' => null,
                'bg_color' => '#F3E5F5',
            ],
            [
                'id' => 'migration-certificate',
                'title' => 'Migration Certificate',
                'title_np' => 'स्थानान्तरण प्रमाणपत्र',
                'image_url' => null,
                'bg_color' => '#FFF8E1',
            ],
        ];

        return response()->json(['success' => true, 'data' => $fallbackEvents]);
    }
}
