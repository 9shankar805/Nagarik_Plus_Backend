<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\User;

class AiService
{
    // Keyword → intent → response map (fallback when OpenAI key not set)
    private array $responses = [
        'passport' => [
            'intent' => 'passport_info',
            'response' => "🛂 **Passport Application Process:**\n\n**Eligibility:** All Nepali citizens above 16 years\n\n**Required Documents:**\n1. Citizenship Certificate\n2. Birth Certificate\n3. Marriage Certificate (if applicable)\n4. Old Passport (for renewal)\n\n**Steps:**\n1. Visit dop.gov.np and create account\n2. Fill online application form\n3. Upload required documents\n4. Pay fee (NPR 5,000 regular / NPR 10,000 express)\n5. Schedule biometrics appointment\n6. Collect passport in 7–21 working days\n\n📞 Helpline: 01-4416000\n🌐 dop.gov.np",
        ],
        'pan' => [
            'intent' => 'pan_info',
            'response' => "🧾 **PAN Card Registration:**\n\n**Eligibility:** Any individual or business in Nepal\n\n**Required Documents:**\n1. Citizenship Certificate\n2. Recent Passport Photo\n3. Business Registration (for businesses)\n\n**Steps:**\n1. Visit ird.gov.np\n2. Fill PAN registration form\n3. Submit documents\n4. Receive PAN in 1–3 working days\n\n✅ Online registration is FREE\n📞 IRD Helpline: 16600101405",
        ],
        'driving license' => [
            'intent' => 'driving_license_info',
            'response' => "🚗 **Driving License Process:**\n\n**Eligibility:**\n- Two-wheelers: 16+ years\n- Four-wheelers: 18+ years\n\n**Required Documents:**\n1. Citizenship Certificate\n2. Medical Certificate\n3. Passport Photo\n4. Blood Group Certificate\n\n**Steps:**\n1. Register at dotm.gov.np\n2. Submit documents\n3. Written test (pass: 60%)\n4. Trial (practical) test\n5. Collect license in 3–7 working days\n\n💰 Fee: NPR 1,500–2,500\n📅 Renewal every 5 years",
        ],
        'national id' => [
            'intent' => 'national_id_info',
            'response' => "🪪 **National ID (NID) Process:**\n\n**Eligibility:** All Nepali citizens above 16 years\n\n**Required Documents:**\n1. Citizenship Certificate\n2. Birth Certificate\n\n**Steps:**\n1. Visit nearest NID enrollment center\n2. Fill application form\n3. Biometric capture (fingerprint + photo)\n4. Verify information\n5. Receive NID in 7–14 working days\n\n✅ NID is FREE\n🌐 nid.gov.np",
        ],
        'company' => [
            'intent' => 'company_registration',
            'response' => "🏢 **Company Registration in Nepal:**\n\n**Eligibility:** Minimum 2 promoters for Pvt. Ltd.\n\n**Required Documents:**\n1. Citizenship of all promoters\n2. Memorandum & Articles of Association\n3. Office lease agreement\n4. PAN of promoters\n\n**Steps:**\n1. Reserve company name at OCR (ocr.gov.np)\n2. Prepare MoA/AoA\n3. Submit registration form\n4. Pay registration fee (NPR 9,000+)\n5. Receive certificate\n6. Get PAN and tax registration\n\n⏱️ Processing: 5–10 working days",
        ],
        'voter' => [
            'intent' => 'voter_registration',
            'response' => "🗳️ **Voter Registration:**\n\n**Eligibility:** Nepali citizens above 18 years\n\n**Required Documents:**\n1. Citizenship Certificate\n2. Recent Photo\n\n**Steps:**\n1. Visit Election Commission office\n2. Fill voter registration form\n3. Submit documents\n4. Verify in voter list\n\n✅ Registration is FREE\n🌐 election.gov.np",
        ],
        'vehicle' => [
            'intent' => 'vehicle_registration',
            'response' => "🚘 **Vehicle Registration (Bluebook):**\n\n**Required Documents:**\n1. Purchase Invoice\n2. Tax Clearance Certificate\n3. Citizenship Certificate\n4. Insurance Certificate\n5. Customs Clearance (for imported vehicles)\n\n**Steps:**\n1. Submit documents to DOTM office\n2. Vehicle technical inspection\n3. Pay registration fee (based on engine cc)\n4. Receive bluebook\n\n⏱️ Processing: 3–5 working days\n🌐 dotm.gov.np",
        ],
        'emergency' => [
            'intent' => 'emergency_info',
            'response' => "🚨 **Emergency Numbers in Nepal:**\n\n🚔 Police: **100**\n🚑 Ambulance: **102**\n🚒 Fire Brigade: **101**\n⛑️ Disaster Management: **1149**\n👮 Traffic Police: **103**\n👩 Women Helpline: **1145**",
        ],
    ];

    /**
     * Process a user message and return AI response
     */
    public function chat(User $user, string $message): array
    {
        $lowerMessage = strtolower($message);
        $response = null;
        $intent = 'general';

        // Try keyword matching first (fast & free)
        foreach ($this->responses as $keyword => $data) {
            if (str_contains($lowerMessage, $keyword)) {
                $response = $data['response'];
                $intent   = $data['intent'];
                break;
            }
        }

        // Fallback response
        if (!$response) {
            $response = "🤔 I don't have specific information on that topic yet.\n\nFor accurate information, please contact:\n\n• **Passport:** dop.gov.np | 01-4416000\n• **Driving License:** dotm.gov.np\n• **Tax/PAN:** ird.gov.np\n• **National ID:** nid.gov.np\n• **Company Reg:** ocr.gov.np\n\nYou can also ask me about:\n- How to apply for passport\n- PAN card registration\n- Driving license process\n- Company registration\n- Emergency contacts";
            $intent = 'unknown';
        }

        // Log conversation
        AiConversation::create([
            'user_id'      => $user->id,
            'user_message' => $message,
            'ai_response'  => $response,
            'intent'       => $intent,
        ]);

        return [
            'message'  => $response,
            'intent'   => $intent,
            'language' => $this->detectLanguage($message),
        ];
    }

    private function detectLanguage(string $text): string
    {
        // Basic Devanagari Unicode range detection
        if (preg_match('/[\x{0900}-\x{097F}]/u', $text)) {
            return 'np';
        }
        return 'en';
    }
}
