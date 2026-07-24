<?php

namespace Database\Seeders;

use App\Models\EmergencyContact;
use Illuminate\Database\Seeder;

class EmergencyContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            ['name' => 'Nepal Police',        'name_np' => 'नेपाल प्रहरी',       'number' => '100',  'description' => 'Emergency Police Line',       'category' => 'police',    'color' => '#1565C0', 'sort_order' => 1],
            ['name' => 'Ambulance',           'name_np' => 'एम्बुलेन्स',          'number' => '102',  'description' => 'Medical Emergency',           'category' => 'ambulance', 'color' => '#D32F2F', 'sort_order' => 2],
            ['name' => 'Fire Brigade',        'name_np' => 'अग्निशमन',            'number' => '101',  'description' => 'Fire Emergency',              'category' => 'fire',      'color' => '#E65100', 'sort_order' => 3],
            ['name' => 'Disaster Management', 'name_np' => 'विपद् व्यवस्थापन',    'number' => '1149', 'description' => 'National Disaster Risk Reduction', 'category' => 'disaster', 'color' => '#6A1B9A', 'sort_order' => 4],
            ['name' => 'Traffic Police',      'name_np' => 'ट्राफिक प्रहरी',       'number' => '103',  'description' => 'Traffic Emergency',           'category' => 'police',    'color' => '#00838F', 'sort_order' => 5],
            ['name' => 'Women Helpline',      'name_np' => 'महिला हेल्पलाइन',      'number' => '1145', 'description' => 'Women & Children Helpline',   'category' => 'health',    'color' => '#AD1457', 'sort_order' => 6],
            ['name' => 'COVID Helpline',      'name_np' => 'स्वास्थ्य हेल्पलाइन', 'number' => '1115', 'description' => 'Health Emergency Helpline',   'category' => 'health',    'color' => '#00695C', 'sort_order' => 7],
            ['name' => 'Nepal Telecom',       'name_np' => 'नेपाल टेलिकम',        'number' => '1498', 'description' => 'Telecom Helpline',            'category' => 'utility',   'color' => '#2E7D32', 'sort_order' => 8],
            ['name' => 'Electricity Authority','name_np'=> 'विद्युत प्राधिकरण',   'number' => '1159', 'description' => 'Power Outage Helpline',       'category' => 'utility',   'color' => '#F9A825', 'sort_order' => 9],
            ['name' => 'Child Helpline',      'name_np' => 'बाल हेल्पलाइन',       'number' => '1098', 'description' => 'Child Protection Helpline',   'category' => 'health',    'color' => '#558B2F', 'sort_order' => 10],
        ];

        foreach ($contacts as $contact) {
            EmergencyContact::updateOrCreate(
                ['number' => $contact['number']],
                $contact
            );
        }
    }
}
