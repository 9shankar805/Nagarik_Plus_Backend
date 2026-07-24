<?php

namespace Database\Seeders;

use App\Models\CitizenService;
use Illuminate\Database\Seeder;

class CitizenServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'slug'        => 'passport',
                'title'       => 'Passport',
                'title_np'    => 'राहदानी',
                'description' => 'Apply for, renew, or update your Nepali passport',
                'category'    => 'identity',
                'icon'        => 'book_rounded',
                'color'       => '#1565C0',
                'eligibility' => 'All Nepali citizens above 16 years of age',
                'required_documents' => [
                    'Citizenship Certificate',
                    'Birth Certificate',
                    'Marriage Certificate (if applicable)',
                    'Old Passport (for renewal)',
                ],
                'application_steps' => [
                    'Visit the Department of Passports website (dop.gov.np)',
                    'Create an account and fill the online form',
                    'Upload required documents',
                    'Pay the applicable fee online',
                    'Schedule biometrics appointment',
                    'Visit the office for biometrics',
                    'Collect passport within 7–21 working days',
                ],
                'fee'             => 'NPR 5,000 (Regular) / NPR 10,000 (Express)',
                'fee_updated_at'  => '2024-01-01',
                'processing_time' => '7–21 working days',
                'official_url'    => 'https://dop.gov.np',
                'faqs'            => [
                    ['q' => 'Can I apply online?', 'a' => 'Yes, visit dop.gov.np to apply online.'],
                    ['q' => 'What is the validity?', 'a' => 'Nepali passport is valid for 10 years.'],
                    ['q' => 'Can I expedite?', 'a' => 'Yes, express service is available for NPR 10,000.'],
                ],
                'sort_order' => 1,
            ],
            [
                'slug'        => 'pan-card',
                'title'       => 'PAN Card',
                'title_np'    => 'स्थायी लेखा नम्बर',
                'description' => 'Register for Permanent Account Number for tax purposes',
                'category'    => 'finance',
                'icon'        => 'receipt_long_rounded',
                'color'       => '#2E7D32',
                'eligibility' => 'Any individual or business entity in Nepal',
                'required_documents' => [
                    'Citizenship Certificate',
                    'Recent Passport Photo',
                    'Business Registration Certificate (for businesses)',
                ],
                'application_steps' => [
                    'Visit IRD website (ird.gov.np)',
                    'Click on PAN Registration',
                    'Fill the registration form',
                    'Upload required documents',
                    'Submit and receive PAN',
                ],
                'fee'             => 'Free (online) / NPR 100 (offline)',
                'fee_updated_at'  => '2024-01-01',
                'processing_time' => '1–3 working days',
                'official_url'    => 'https://ird.gov.np',
                'faqs'            => [
                    ['q' => 'Is PAN mandatory?', 'a' => 'Yes, for all employed individuals and businesses.'],
                    ['q' => 'Can I register online?', 'a' => 'Yes, at ird.gov.np completely free.'],
                ],
                'sort_order' => 2,
            ],
            [
                'slug'        => 'national-id',
                'title'       => 'National ID',
                'title_np'    => 'राष्ट्रिय परिचय पत्र',
                'description' => 'Biometric National Identity Card for all Nepali citizens',
                'category'    => 'identity',
                'icon'        => 'badge_rounded',
                'color'       => '#6A1B9A',
                'eligibility' => 'All Nepali citizens above 16 years',
                'required_documents' => [
                    'Citizenship Certificate',
                    'Birth Certificate',
                ],
                'application_steps' => [
                    'Visit nearest NID enrollment center',
                    'Fill application form',
                    'Biometric capture (fingerprint + photo)',
                    'Verify your information',
                    'Receive NID card in 7–14 working days',
                ],
                'fee'             => 'Free',
                'fee_updated_at'  => '2024-01-01',
                'processing_time' => '7–14 working days',
                'official_url'    => 'https://nid.gov.np',
                'faqs'            => [
                    ['q' => 'Is NID mandatory?', 'a' => 'It will be mandatory for all government services.'],
                ],
                'sort_order' => 3,
            ],
            [
                'slug'        => 'driving-license',
                'title'       => 'Driving License',
                'title_np'    => 'सवारी चालक अनुमतिपत्र',
                'description' => 'Apply for or renew your driving license',
                'category'    => 'vehicle',
                'icon'        => 'drive_eta_rounded',
                'color'       => '#F57F17',
                'eligibility' => '16+ years for two-wheelers, 18+ years for four-wheelers',
                'required_documents' => [
                    'Citizenship Certificate',
                    'Medical Certificate from registered hospital',
                    'Passport-size Photos',
                    'Blood Group Certificate',
                ],
                'application_steps' => [
                    'Register on DOTM website (dotm.gov.np)',
                    'Submit required documents',
                    'Appear for written test (pass: 60%)',
                    'Complete trial (practical) test',
                    'Collect license in 3–7 working days',
                ],
                'fee'             => 'NPR 1,500–2,500 (based on category)',
                'fee_updated_at'  => '2024-01-01',
                'processing_time' => '3–7 working days after passing tests',
                'official_url'    => 'https://dotm.gov.np',
                'faqs'            => [
                    ['q' => 'How often to renew?', 'a' => 'Every 5 years.'],
                    ['q' => 'Multiple categories?', 'a' => 'Yes, you can apply for A, B, C categories.'],
                ],
                'sort_order' => 4,
            ],
            [
                'slug'        => 'voter-registration',
                'title'       => 'Voter Registration',
                'title_np'    => 'मतदाता नामावली',
                'description' => 'Register or update voter details',
                'category'    => 'legal',
                'icon'        => 'how_to_vote_rounded',
                'color'       => '#D32F2F',
                'eligibility' => 'Nepali citizens above 18 years',
                'required_documents' => ['Citizenship Certificate', 'Recent Photo'],
                'application_steps' => [
                    'Visit Election Commission office',
                    'Fill voter registration form',
                    'Submit required documents',
                    'Verify your name in voter list',
                ],
                'fee'             => 'Free',
                'fee_updated_at'  => '2024-01-01',
                'processing_time' => 'Seasonal (announced before elections)',
                'official_url'    => 'https://election.gov.np',
                'faqs'            => [
                    ['q' => 'How to check my name?', 'a' => 'Visit election.gov.np voter search.'],
                ],
                'sort_order' => 5,
            ],
            [
                'slug'        => 'company-registration',
                'title'       => 'Company Registration',
                'title_np'    => 'कम्पनी दर्ता',
                'description' => 'Register Private Limited or Public company at OCR',
                'category'    => 'business',
                'icon'        => 'business_rounded',
                'color'       => '#1565C0',
                'eligibility' => 'Minimum 2 promoters for Private Limited company',
                'required_documents' => [
                    'Citizenship of all promoters',
                    'Memorandum of Association (MoA)',
                    'Articles of Association (AoA)',
                    'Office lease agreement',
                    'PAN of all promoters',
                ],
                'application_steps' => [
                    'Reserve company name at OCR (ocr.gov.np)',
                    'Prepare MoA and AoA documents',
                    'Submit registration form with documents',
                    'Pay registration fee',
                    'Receive company registration certificate',
                    'Register for PAN and tax',
                    'Open company bank account',
                ],
                'fee'             => 'NPR 9,000 onwards (based on share capital)',
                'fee_updated_at'  => '2024-01-01',
                'processing_time' => '5–10 working days',
                'official_url'    => 'https://ocr.gov.np',
                'faqs'            => [
                    ['q' => 'Minimum capital?', 'a' => 'NPR 1 for private limited company.'],
                    ['q' => 'Can foreigners register?', 'a' => 'Yes, with FITTA approval for FDI.'],
                ],
                'sort_order' => 6,
            ],
        ];

        foreach ($services as $service) {
            CitizenService::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
