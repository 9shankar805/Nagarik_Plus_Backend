<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title'        => 'File Police Reports Easily via Nagarik+',
                'title_np'     => 'नागरिक+ मार्फत सजिलै प्रहरी रिपोर्ट फाइल गर्नुहोस्',
                'description'  => 'Report incidents from anywhere with just a few taps.',
                'image_url'    => null,
                'link_type'    => 'none',
                'link_value'   => null,
                'is_active'    => true,
                'sort_order'   => 1,
            ],
            [
                'title'        => 'Store Your Documents Securely in Digital Locker',
                'title_np'     => 'डिजिटल लकरमा आफ्ना कागजातहरू सुरक्षित राख्नुहोस्',
                'description'  => 'Keep all your important documents in one place.',
                'image_url'    => null,
                'link_type'    => 'none',
                'link_value'   => null,
                'is_active'    => true,
                'sort_order'   => 2,
            ],
            [
                'title'        => 'Government Services All in One Place',
                'title_np'     => 'सरकारी सेवाहरू एकै ठाउँमा',
                'description'  => 'Passport, PAN, Citizenship - all from the app.',
                'image_url'    => null,
                'link_type'    => 'none',
                'link_value'   => null,
                'is_active'    => true,
                'sort_order'   => 3,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
