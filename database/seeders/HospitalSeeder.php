<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hospital;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        $hospitals = [
            // ── Kathmandu Valley ─────────────────────────────────────────
            ['name' => 'Teaching Hospital', 'name_np' => 'शिक्षण अस्पताल', 'address' => 'Maharajgunj, Kathmandu', 'address_np' => 'महाराजगञ्ज, काठमाडौं', 'phone' => '01-4412765', 'type' => 'Government', 'latitude' => 27.7345, 'longitude' => 85.3283],
            ['name' => 'Bir Hospital', 'name_np' => 'वीर अस्पताल', 'address' => 'Tundikhel, Kathmandu', 'address_np' => 'टुण्डिखेल, काठमाडौं', 'phone' => '01-4261944', 'type' => 'Government', 'latitude' => 27.7058, 'longitude' => 85.3146],
            ['name' => 'Patan Hospital', 'name_np' => 'पाटन अस्पताल', 'address' => 'Lagankhel, Lalitpur', 'address_np' => 'लगनखेल, ललितपुर', 'phone' => '01-5522266', 'type' => 'Private', 'latitude' => 27.6667, 'longitude' => 85.3167],
            ['name' => 'Norvic Hospital', 'name_np' => 'नोरभिक अस्पताल', 'address' => 'Thapathali, Kathmandu', 'address_np' => 'थापाथली, काठमाडौं', 'phone' => '01-4258554', 'type' => 'Private', 'latitude' => 27.6939, 'longitude' => 85.3141],
            ['name' => 'Bhaktapur Hospital', 'name_np' => 'भक्तपुर अस्पताल', 'address' => 'Bhaktapur', 'address_np' => 'भक्तपुर', 'phone' => '01-6610798', 'type' => 'Government', 'latitude' => 27.6710, 'longitude' => 85.4298],
            ['name' => 'Grande International Hospital', 'name_np' => 'ग्रान्डे अन्तर्राष्ट्रिय अस्पताल', 'address' => 'Tokha, Kathmandu', 'address_np' => 'टोखा, काठमाडौं', 'phone' => '01-5159266', 'type' => 'Private', 'latitude' => 27.7574, 'longitude' => 85.3200],
            ['name' => 'Kanti Children\'s Hospital', 'name_np' => 'काँटी बाल अस्पताल', 'address' => 'Maharajgunj, Kathmandu', 'address_np' => 'महाराजगञ्ज, काठमाडौं', 'phone' => '01-4412691', 'type' => 'Government', 'latitude' => 27.7356, 'longitude' => 85.3260],
            ['name' => 'National Trauma Centre', 'name_np' => 'राष्ट्रिय ट्रमा केन्द्र', 'address' => 'Maharajgunj, Kathmandu', 'address_np' => 'महाराजगञ्ज, काठमाडौं', 'phone' => '01-4419980', 'type' => 'Government', 'latitude' => 27.7330, 'longitude' => 85.3270],
            ['name' => 'Shahid Gangalal National Heart Centre', 'name_np' => 'शहीद गंगालाल राष्ट्रिय हृदय केन्द्र', 'address' => 'Bansbari, Kathmandu', 'address_np' => 'बाँसबारी, काठमाडौं', 'phone' => '01-4371322', 'type' => 'Government', 'latitude' => 27.7433, 'longitude' => 85.3349],
            ['name' => 'Mediciti Hospital', 'name_np' => 'मेडिसिटी अस्पताल', 'address' => 'Nayabazar, Kathmandu', 'address_np' => 'नयाँबजार, काठमाडौं', 'phone' => '01-4374344', 'type' => 'Private', 'latitude' => 27.7200, 'longitude' => 85.2900],

            // ── Pokhara ───────────────────────────────────────────────────
            ['name' => 'Gandaki Provincial Hospital', 'name_np' => 'गण्डकी प्रादेशिक अस्पताल', 'address' => 'Pokhara, Kaski', 'address_np' => 'पोखरा, कास्की', 'phone' => '061-521009', 'type' => 'Government', 'latitude' => 28.2380, 'longitude' => 83.9956],
            ['name' => 'Western Regional Hospital', 'name_np' => 'पश्चिमाञ्चल क्षेत्रीय अस्पताल', 'address' => 'Pokhara, Kaski', 'address_np' => 'पोखरा, कास्की', 'phone' => '061-522066', 'type' => 'Government', 'latitude' => 28.2096, 'longitude' => 83.9856],
            ['name' => 'Manipal Teaching Hospital', 'name_np' => 'मणिपाल शिक्षण अस्पताल', 'address' => 'Phulbari, Pokhara', 'address_np' => 'फुलबारी, पोखरा', 'phone' => '061-526416', 'type' => 'Private', 'latitude' => 28.2550, 'longitude' => 83.9750],

            // ── Chitwan ───────────────────────────────────────────────────
            ['name' => 'Bharatpur Hospital', 'name_np' => 'भरतपुर अस्पताल', 'address' => 'Bharatpur, Chitwan', 'address_np' => 'भरतपुर, चितवन', 'phone' => '056-527999', 'type' => 'Government', 'latitude' => 27.6766, 'longitude' => 84.4297],
            ['name' => 'College of Medical Sciences', 'name_np' => 'कलेज अफ मेडिकल साइन्सेज', 'address' => 'Bharatpur, Chitwan', 'address_np' => 'भरतपुर, चितवन', 'phone' => '056-524812', 'type' => 'Private', 'latitude' => 27.6900, 'longitude' => 84.4300],

            // ── Birgunj / Parsa ───────────────────────────────────────────
            ['name' => 'Narayani Zonal Hospital', 'name_np' => 'नारायणी अञ्चल अस्पताल', 'address' => 'Birgunj, Parsa', 'address_np' => 'वीरगञ्ज, पर्सा', 'phone' => '051-520111', 'type' => 'Government', 'latitude' => 27.0122, 'longitude' => 84.8778],

            // ── Butwal / Rupandehi ────────────────────────────────────────
            ['name' => 'Lumbini Provincial Hospital', 'name_np' => 'लुम्बिनी प्रादेशिक अस्पताल', 'address' => 'Butwal, Rupandehi', 'address_np' => 'बुटवल, रुपन्देही', 'phone' => '071-540233', 'type' => 'Government', 'latitude' => 27.7005, 'longitude' => 83.4484],

            // ── Dhangadhi / Kailali ───────────────────────────────────────
            ['name' => 'Seti Provincial Hospital', 'name_np' => 'सेती प्रादेशिक अस्पताल', 'address' => 'Dhangadhi, Kailali', 'address_np' => 'धनगढी, कैलाली', 'phone' => '091-521001', 'type' => 'Government', 'latitude' => 28.6833, 'longitude' => 80.6000],

            // ── Biratnagar / Morang ───────────────────────────────────────
            ['name' => 'BP Koirala Institute of Health Sciences', 'name_np' => 'बी.पी. कोइराला स्वास्थ्य विज्ञान प्रतिष्ठान', 'address' => 'Dharan, Sunsari', 'address_np' => 'धरान, सुनसरी', 'phone' => '025-525555', 'type' => 'Government', 'latitude' => 26.8065, 'longitude' => 87.2846],
            ['name' => 'Koshi Zonal Hospital', 'name_np' => 'कोशी अञ्चल अस्पताल', 'address' => 'Biratnagar, Morang', 'address_np' => 'विराटनगर, मोरङ', 'phone' => '021-525858', 'type' => 'Government', 'latitude' => 26.4525, 'longitude' => 87.2718],

            // ── Surkhet / Karnali ─────────────────────────────────────────
            ['name' => 'Karnali Provincial Hospital', 'name_np' => 'कर्णाली प्रादेशिक अस्पताल', 'address' => 'Surkhet, Birendranagar', 'address_np' => 'सुर्खेत, वीरेन्द्रनगर', 'phone' => '083-520233', 'type' => 'Government', 'latitude' => 28.5971, 'longitude' => 81.6060],

            // ── Hetauda / Bagmati Province ────────────────────────────────
            ['name' => 'Hetauda Hospital', 'name_np' => 'हेटौँडा अस्पताल', 'address' => 'Hetauda, Makwanpur', 'address_np' => 'हेटौँडा, मकवानपुर', 'phone' => '057-520277', 'type' => 'Government', 'latitude' => 27.4167, 'longitude' => 85.0333],
        ];

        foreach ($hospitals as $data) {
            Hospital::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['is_active' => true])
            );
        }

        $this->command->info('✅ HospitalSeeder: ' . count($hospitals) . ' hospitals seeded.');
    }
}
