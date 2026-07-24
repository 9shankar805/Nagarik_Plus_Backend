<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [
            // Passport offices
            ['name' => 'Department of Passports – Narayanhiti',   'category' => 'passport',   'address' => 'Narayanhiti, Kathmandu',     'district' => 'Kathmandu', 'province' => 'Bagmati',    'latitude' => 27.7157, 'longitude' => 85.3152, 'phone' => '01-4416000', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],
            ['name' => 'Pokhara Passport Office',                  'category' => 'passport',   'address' => 'Prithvipath, Pokhara',       'district' => 'Kaski',     'province' => 'Gandaki',    'latitude' => 28.2096, 'longitude' => 83.9856, 'phone' => '061-521000', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],
            ['name' => 'Butwal Passport Office',                   'category' => 'passport',   'address' => 'Butwal, Rupandehi',          'district' => 'Rupandehi', 'province' => 'Lumbini',    'latitude' => 27.7006, 'longitude' => 83.4485, 'phone' => '071-547000', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],
            ['name' => 'Biratnagar Passport Office',               'category' => 'passport',   'address' => 'Biratnagar Metropolitan',    'district' => 'Morang',    'province' => 'Koshi',      'latitude' => 26.4525, 'longitude' => 87.2718, 'phone' => '021-470000', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],

            // Transport offices
            ['name' => 'Dept. of Transport Management – Minbhawan','category' => 'transport', 'address' => 'Minbhawan, Kathmandu',       'district' => 'Kathmandu', 'province' => 'Bagmati',    'latitude' => 27.6910, 'longitude' => 85.3400, 'phone' => '01-4480204', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],
            ['name' => 'Ekantakuna Transport Office',               'category' => 'transport', 'address' => 'Ekantakuna, Lalitpur',       'district' => 'Lalitpur',  'province' => 'Bagmati',    'latitude' => 27.6600, 'longitude' => 85.3200, 'phone' => '01-5530000', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],
            ['name' => 'Pokhara Transport Office',                  'category' => 'transport', 'address' => 'Nayabazar, Pokhara',         'district' => 'Kaski',     'province' => 'Gandaki',    'latitude' => 28.2050, 'longitude' => 83.9910, 'phone' => '061-522000', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],

            // Tax / IRD offices
            ['name' => 'Inland Revenue Dept. – Lazimpat',          'category' => 'tax',       'address' => 'Lazimpat, Kathmandu',        'district' => 'Kathmandu', 'province' => 'Bagmati',    'latitude' => 27.7200, 'longitude' => 85.3185, 'phone' => '01-4415802', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],
            ['name' => 'Inland Revenue Office – Kalanki',          'category' => 'tax',       'address' => 'Kalanki, Kathmandu',         'district' => 'Kathmandu', 'province' => 'Bagmati',    'latitude' => 27.6940, 'longitude' => 85.2830, 'phone' => '01-4278000', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],
            ['name' => 'IRD Pokhara',                              'category' => 'tax',       'address' => 'Chipledhunga, Pokhara',      'district' => 'Kaski',     'province' => 'Gandaki',    'latitude' => 28.2100, 'longitude' => 83.9900, 'phone' => '061-520000', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],

            // Municipalities
            ['name' => 'Kathmandu Metropolitan City',              'category' => 'municipality','address' => 'Bagmati Zone, Kathmandu',  'district' => 'Kathmandu', 'province' => 'Bagmati',    'latitude' => 27.7041, 'longitude' => 85.3145, 'phone' => '01-4270000', 'office_hours' => '10:00 AM – 4:00 PM (Sun–Fri)'],
            ['name' => 'Lalitpur Metropolitan City',               'category' => 'municipality','address' => 'Lalitpur Metropolitan',    'district' => 'Lalitpur',  'province' => 'Bagmati',    'latitude' => 27.6644, 'longitude' => 85.3188, 'phone' => '01-5522266', 'office_hours' => '10:00 AM – 4:00 PM (Sun–Fri)'],
            ['name' => 'Pokhara Metropolitan City',                'category' => 'municipality','address' => 'Pokhara Metropolitan',     'district' => 'Kaski',     'province' => 'Gandaki',    'latitude' => 28.2096, 'longitude' => 83.9856, 'phone' => '061-521001', 'office_hours' => '10:00 AM – 4:00 PM (Sun–Fri)'],

            // Police
            ['name' => 'Nepal Police HQ – Naxal',                  'category' => 'police',    'address' => 'Naxal, Kathmandu',           'district' => 'Kathmandu', 'province' => 'Bagmati',    'latitude' => 27.7172, 'longitude' => 85.3240, 'phone' => '100',        'office_hours' => '24/7'],
            ['name' => 'Metropolitan Police – Ranipokhari',         'category' => 'police',    'address' => 'Ranipokhari, Kathmandu',     'district' => 'Kathmandu', 'province' => 'Bagmati',    'latitude' => 27.7093, 'longitude' => 85.3156, 'phone' => '01-4223100', 'office_hours' => '24/7'],

            // District Administration
            ['name' => 'District Administration Office – Kathmandu','category' => 'administration','address' => 'Babarmahal, Kathmandu', 'district' => 'Kathmandu', 'province' => 'Bagmati',   'latitude' => 27.6980, 'longitude' => 85.3190, 'phone' => '01-4224374', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],
            ['name' => 'District Administration Office – Lalitpur', 'category' => 'administration','address' => 'Pulchowk, Lalitpur',    'district' => 'Lalitpur',  'province' => 'Bagmati',   'latitude' => 27.6700, 'longitude' => 85.3100, 'phone' => '01-5524000', 'office_hours' => '10:00 AM – 5:00 PM (Sun–Fri)'],
        ];

        foreach ($offices as $office) {
            Office::updateOrCreate(
                ['name' => $office['name']],
                array_merge($office, ['is_active' => true])
            );
        }
    }
}
