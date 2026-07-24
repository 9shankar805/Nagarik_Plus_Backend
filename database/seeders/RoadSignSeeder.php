<?php

namespace Database\Seeders;

use App\Models\RoadSign;
use Illuminate\Database\Seeder;

class RoadSignSeeder extends Seeder
{
    public function run(): void
    {
        $signs = [
            // Mandatory
            ['name' => 'Stop',            'name_np' => 'रोक्नुहोस्',        'meaning' => 'You must stop your vehicle completely before the line.',             'category' => 'mandatory',   'color_code' => '#D32F2F'],
            ['name' => 'No Entry',        'name_np' => 'प्रवेश निषेध',       'meaning' => 'Entry is prohibited for all vehicles.',                              'category' => 'mandatory',   'color_code' => '#D32F2F'],
            ['name' => 'Speed Limit',     'name_np' => 'गति सीमा',           'meaning' => 'Do not exceed the indicated speed limit.',                            'category' => 'mandatory',   'color_code' => '#D32F2F'],
            ['name' => 'No Overtaking',   'name_np' => 'ओभरटेक निषेध',      'meaning' => 'Overtaking other vehicles is strictly prohibited here.',              'category' => 'mandatory',   'color_code' => '#D32F2F'],
            ['name' => 'No U-Turn',       'name_np' => 'यू-टर्न निषेध',      'meaning' => 'U-turns are not permitted at this location.',                        'category' => 'mandatory',   'color_code' => '#D32F2F'],
            ['name' => 'Turn Right Only', 'name_np' => 'दाहिने मोड्नुहोस्', 'meaning' => 'You must turn right at this intersection.',                          'category' => 'mandatory',   'color_code' => '#1565C0'],
            ['name' => 'Give Way',        'name_np' => 'बाटो दिनुहोस्',      'meaning' => 'Give way to vehicles on the main road.',                             'category' => 'mandatory',   'color_code' => '#D32F2F'],

            // Warning
            ['name' => 'Road Work Ahead',     'name_np' => 'सडक निर्माण',         'meaning' => 'Road construction ahead. Slow down and proceed with caution.',       'category' => 'warning', 'color_code' => '#F9A825'],
            ['name' => 'School Zone',         'name_np' => 'विद्यालय क्षेत्र',    'meaning' => 'School is nearby. Watch for children crossing.',                      'category' => 'warning', 'color_code' => '#F9A825'],
            ['name' => 'Slippery Road',       'name_np' => 'चिप्लो सडक',          'meaning' => 'Road surface may be slippery. Reduce speed.',                        'category' => 'warning', 'color_code' => '#F9A825'],
            ['name' => 'Railway Crossing',    'name_np' => 'रेलवे क्रसिङ',        'meaning' => 'Railway crossing ahead. Stop and check both sides.',                 'category' => 'warning', 'color_code' => '#F9A825'],
            ['name' => 'Animals on Road',     'name_np' => 'सडकमा जनावर',         'meaning' => 'Animals may be crossing. Watch out and slow down.',                  'category' => 'warning', 'color_code' => '#F9A825'],
            ['name' => 'Sharp Curve',         'name_np' => 'तीखो घुम्ती',          'meaning' => 'Sharp curve ahead. Reduce speed and stay in lane.',                  'category' => 'warning', 'color_code' => '#F9A825'],
            ['name' => 'Roundabout Ahead',    'name_np' => 'राउन्डअबाउट',          'meaning' => 'Roundabout ahead. Give way to vehicles already on it.',              'category' => 'warning', 'color_code' => '#F9A825'],

            // Informatory
            ['name' => 'Hospital',        'name_np' => 'अस्पताल',            'meaning' => 'Hospital or medical facility ahead.',                                'category' => 'informatory', 'color_code' => '#2E7D32'],
            ['name' => 'Fuel Station',    'name_np' => 'इन्धन स्टेशन',       'meaning' => 'Fuel/petrol station available ahead.',                               'category' => 'informatory', 'color_code' => '#2E7D32'],
            ['name' => 'Parking',         'name_np' => 'पार्किङ',            'meaning' => 'Parking area available.',                                            'category' => 'informatory', 'color_code' => '#1565C0'],
            ['name' => 'Restaurant',      'name_np' => 'रेस्टुरेन्ट',         'meaning' => 'Food and restaurant facilities available ahead.',                    'category' => 'informatory', 'color_code' => '#2E7D32'],
            ['name' => 'Emergency Phone', 'name_np' => 'आपतकालीन फोन',       'meaning' => 'Emergency telephone is available at this point.',                    'category' => 'informatory', 'color_code' => '#1565C0'],
            ['name' => 'One Way',         'name_np' => 'एकतर्फी',            'meaning' => 'This is a one-way road. Do not enter from opposite direction.',       'category' => 'informatory', 'color_code' => '#1565C0'],
        ];

        foreach ($signs as $sign) {
            RoadSign::create(array_merge($sign, ['is_active' => true]));
        }
    }
}
