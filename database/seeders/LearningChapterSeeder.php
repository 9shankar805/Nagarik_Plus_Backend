<?php

namespace Database\Seeders;

use App\Models\LearningCategory;
use App\Models\LearningChapter;
use Illuminate\Database\Seeder;

class LearningChapterSeeder extends Seeder
{
    public function run(): void
    {
        $categories = LearningCategory::pluck('id', 'slug');

        $chapters = $this->getChapters($categories);

        foreach ($chapters as $chapter) {
            LearningChapter::updateOrCreate(
                [
                    'learning_category_id' => $chapter['learning_category_id'],
                    'title_en'             => $chapter['title_en'],
                ],
                $chapter
            );
        }

        $this->command->info('✅ LearningChapterSeeder: ' . count($chapters) . ' chapters seeded.');
    }

    private function getChapters(array|\Illuminate\Support\Collection $cats): array
    {
        return array_merge(
            $this->drivingChapters($cats['driving_license'] ?? null),
            $this->loksewhaChapters($cats['loksewa'] ?? null),
            $this->financeChapters($cats['finance'] ?? null),
            $this->rightsChapters($cats['rights'] ?? null),
            $this->competitiveChapters($cats['competitive'] ?? null),
        );
    }

    private function drivingChapters($catId): array
    {
        if (!$catId) return [];
        $base = ['learning_category_id' => $catId, 'is_published' => true, 'published_at' => now()];
        return [
            $base + ['display_order' => 1, 'read_time_minutes' => 8, 'title_en' => 'Traffic Signs & Signals', 'title_np' => 'ट्राफिक चिह्न र संकेत',
                'summary_en' => 'Mandatory, warning, and informatory signs used on Nepal roads.',
                'summary_np' => 'नेपालका सडकमा प्रयोग हुने अनिवार्य, चेतावनी र सूचनात्मक चिह्नहरू।',
                'content_en' => "<h2>Traffic Signs in Nepal</h2><p>Traffic signs are divided into three categories:</p><ul><li><strong>Mandatory Signs (Red Circle)</strong> – Must be obeyed. Examples: Stop, No Entry, Speed Limit, No Overtaking.</li><li><strong>Warning Signs (Yellow/Orange Triangle)</strong> – Alert drivers to hazards. Examples: Sharp Bend, School Ahead, Slippery Road.</li><li><strong>Informatory Signs (Blue/Green Rectangle)</strong> – Provide guidance. Examples: Hospital, Parking, Direction signs.</li></ul><h3>Hand Signals</h3><p>When traffic lights fail, traffic police use hand signals. Right arm extended = stop. Circular motion = proceed. Arm raised = stop from behind.</p>",
                'content_np' => "<h2>नेपालमा ट्राफिक चिह्नहरू</h2><p>ट्राफिक चिह्नहरू तीन वर्गमा विभाजित छन्:</p><ul><li><strong>अनिवार्य चिह्न (रातो गोलो)</strong> – पालना गर्नै पर्ने। उदाहरण: रोक, प्रवेश निषेध, गति सीमा।</li><li><strong>चेतावनी चिह्न (पहेँलो/सुन्तला त्रिकोण)</strong> – खतराको सूचना। उदाहरण: तीव्र मोड, स्कूल अगाडि।</li><li><strong>सूचनात्मक चिह्न (नीलो/हरियो आयत)</strong> – मार्गदर्शन। उदाहरण: अस्पताल, पार्किङ।</li></ul>"],
            $base + ['display_order' => 2, 'read_time_minutes' => 6, 'title_en' => 'Road Rules & Regulations', 'title_np' => 'सडक नियम तथा विनियम',
                'summary_en' => 'Speed limits, overtaking rules, lane discipline, and right of way.',
                'summary_np' => 'गति सीमा, ओभरटेक नियम, लेन अनुशासन र अग्राधिकार।',
                'content_en' => "<h2>Key Road Rules in Nepal</h2><ul><li><strong>Speed Limits:</strong> Urban areas 40 km/h, highways 80 km/h, hills 30 km/h.</li><li><strong>Overtaking:</strong> Only on the right side. Never on blind corners, bridges, or near intersections.</li><li><strong>Lane Discipline:</strong> Keep left except when overtaking. Heavy vehicles must use leftmost lane.</li><li><strong>Right of Way:</strong> Vehicles on main road have priority. Pedestrians at zebra crossings have right of way.</li><li><strong>Seatbelts:</strong> Mandatory for driver and front passenger.</li><li><strong>Mobile Phones:</strong> Using handheld phone while driving is prohibited.</li></ul>",
                'content_np' => "<h2>नेपालमा प्रमुख सडक नियमहरू</h2><ul><li><strong>गति सीमा:</strong> सहरी क्षेत्र ४० कि.मी./घन्टा, राजमार्ग ८० कि.मी./घन्टा, पहाड ३० कि.मी./घन्टा।</li><li><strong>ओभरटेक:</strong> दाहिने तर्फबाट मात्र। अन्धा मोड, पुल वा चोकनजिक कहिल्यै नगर्ने।</li><li><strong>सिट बेल्ट:</strong> चालक र अगाडि बस्ने यात्रीलाई अनिवार्य।</li></ul>"],
            $base + ['display_order' => 3, 'read_time_minutes' => 7, 'title_en' => 'Vehicle Categories & Licenses', 'title_np' => 'सवारी श्रेणी र लाइसेन्स',
                'summary_en' => 'Understanding A, B, C, D license categories and what vehicles they cover.',
                'summary_np' => 'A, B, C, D लाइसेन्स श्रेणी र ती अन्तर्गत पर्ने सवारीहरू।',
                'content_en' => "<h2>License Categories in Nepal</h2><ul><li><strong>Category A:</strong> Motorcycles and scooters up to 50cc. Minimum age 16.</li><li><strong>Category B:</strong> Light vehicles — cars, jeeps, pickups up to 3500kg. Minimum age 18.</li><li><strong>Category C:</strong> Medium vehicles — trucks, minibuses. Must hold B for 2 years first.</li><li><strong>Category D:</strong> Heavy vehicles — large buses, heavy trucks. Must hold C for 2 years.</li></ul><h3>License Validity</h3><p>Nepal driving licenses are valid for 5 years and must be renewed before expiry at the concerned Transport Management Office.</p>",
                'content_np' => "<h2>नेपालमा लाइसेन्स श्रेणीहरू</h2><ul><li><strong>श्रेणी क:</strong> मोटरसाइकल र स्कूटर ५०cc सम्म। न्यूनतम उमेर १६।</li><li><strong>श्रेणी ख:</strong> हलुका सवारी — कार, जीप, पिकअप ३५०० के.जी. सम्म। न्यूनतम उमेर १८।</li><li><strong>श्रेणी ग:</strong> मध्यम सवारी — ट्रक, मिनिबस।</li><li><strong>श्रेणी घ:</strong> भारी सवारी — ठूला बस, भारी ट्रक।</li></ul>"],
            $base + ['display_order' => 4, 'read_time_minutes' => 5, 'title_en' => 'Practical Trial Tips (8-Shape & Ramp)', 'title_np' => 'प्रयोगात्मक ट्रायल सुझाव',
                'summary_en' => 'How to pass the practical driving trial: 8-shape, ramp, and reverse parking.',
                'summary_np' => '८ आकार, उकालो-ओरालो र रिभर्स पार्किङ ट्रायल पास गर्ने तरिका।',
                'content_en' => "<h2>Practical Trial Guide</h2><h3>8-Shape Test</h3><p>Drive in a figure-8 pattern within marked boundaries. Tips: Use first gear, balance clutch and accelerator, look ahead not at the wheels.</p><h3>Ramp/Hill Test</h3><p>Stop on incline, then move forward without rolling back. Use handbrake technique: release handbrake as you engage clutch friction point.</p><h3>Reverse Parking</h3><p>Use mirrors and check blind spots. Align with the parking bay before reversing. Full lock when rear wheel passes the cone.</p>",
                'content_np' => "<h2>प्रयोगात्मक ट्रायल गाइड</h2><h3>८ आकार परीक्षण</h3><p>चिह्नित सीमाभित्र ८ आकारमा गाडी चलाउनुस्। सुझाव: पहिलो गियर, क्लच र एक्सेलेरेटर सन्तुलन गर्नुस्।</p><h3>उकालो-ओरालो परीक्षण</h3><p>ढलानमा रोक्नुस् र पछाडि नखस्कीकन अगाडि जानुस्। ह्यान्डब्रेक प्रविधि: क्लच फ्रिक्सन बिन्दुमा ह्यान्डब्रेक छोड्नुस्।</p>"],
            $base + ['display_order' => 5, 'read_time_minutes' => 4, 'title_en' => 'Emergency Procedures & First Aid', 'title_np' => 'आपतकालीन प्रक्रिया र प्राथमिक उपचार',
                'summary_en' => 'What to do in case of an accident, breakdown, and basic first aid duties.',
                'summary_np' => 'दुर्घटना, ब्रेकडाउन भएमा के गर्ने र आधारभूत प्राथमिक उपचार कर्तव्य।',
                'content_en' => "<h2>Emergency Procedures</h2><h3>In Case of Accident</h3><ol><li>Stop vehicle safely and turn on hazard lights.</li><li>Call 100 (Police) or 102 (Ambulance).</li><li>Do not move injured persons unless there is fire risk.</li><li>Provide basic first aid — control bleeding with pressure.</li><li>Do not leave the scene — it is a legal obligation to assist.</li></ol><h3>Vehicle Breakdown</h3><p>Pull off the road, place warning triangles 50m behind the vehicle, turn on hazard lights, and call for assistance.</p>",
                'content_np' => "<h2>आपतकालीन प्रक्रियाहरू</h2><h3>दुर्घटना भएमा</h3><ol><li>गाडी सुरक्षित ठाउँमा रोक्नुस् र ह्याजार्ड बत्ती बाल्नुस्।</li><li>१०० (प्रहरी) वा १०२ (एम्बुलेन्स) मा फोन गर्नुस्।</li><li>आगो नलागेसम्म घाइतेलाई नसार्नुस्।</li></ol>"],
        ];
    }

    private function loksewhaChapters($catId): array
    {
        if (!$catId) return [];
        $base = ['learning_category_id' => $catId, 'is_published' => true, 'published_at' => now()];
        return [
            $base + ['display_order' => 1, 'read_time_minutes' => 10, 'title_en' => 'Nepal Constitution & Governance', 'title_np' => 'नेपाल संविधान र शासन',
                'summary_en' => 'Key provisions of Nepal Constitution 2072, government structure, and fundamental rights.',
                'summary_np' => 'नेपाल संविधान २०७२ का प्रमुख व्यवस्था, सरकारी संरचना र मौलिक हक।',
                'content_en' => "<h2>Nepal Constitution 2072 (2015)</h2><p>Nepal adopted a new constitution on Ashwin 3, 2072 BS (September 20, 2015). Key features:</p><ul><li><strong>Federal Structure:</strong> 3 tiers — Federal, Provincial (7 provinces), Local (753 local bodies)</li><li><strong>State Religion:</strong> Secular state</li><li><strong>Government Type:</strong> Federal Democratic Republic</li><li><strong>Parliament:</strong> Bicameral — House of Representatives (275 members) and National Assembly (59 members)</li><li><strong>Fundamental Rights:</strong> 31 rights including Right to Equality, Freedom, Education, Health, Food, Housing</li></ul><h3>Three Organs of State</h3><ol><li><strong>Legislature:</strong> Makes laws</li><li><strong>Executive:</strong> Implements laws (Council of Ministers led by PM)</li><li><strong>Judiciary:</strong> Interprets laws (Supreme Court, High Courts, District Courts)</li></ol>",
                'content_np' => "<h2>नेपाल संविधान २०७२</h2><p>नेपालले असोज ३, २०७२ मा नयाँ संविधान जारी गर्यो। प्रमुख विशेषताहरू:</p><ul><li><strong>संघीय संरचना:</strong> ३ तह — संघ, प्रदेश (७ प्रदेश), स्थानीय (७५३ स्थानीय तह)</li><li><strong>संसद:</strong> द्विसदनीय — प्रतिनिधि सभा (२७५ सदस्य) र राष्ट्रिय सभा (५९ सदस्य)</li></ul>"],
            $base + ['display_order' => 2, 'read_time_minutes' => 8, 'title_en' => 'Nepal History & Geography', 'title_np' => 'नेपालको इतिहास र भूगोल',
                'summary_en' => 'Important historical events, geography, rivers, mountains, and districts of Nepal.',
                'summary_np' => 'महत्त्वपूर्ण ऐतिहासिक घटनाहरू, भूगोल, नदी, पहाड र नेपालका जिल्लाहरू।',
                'content_en' => "<h2>Nepal — Geographic Overview</h2><ul><li>Area: 147,181 sq km</li><li>Location: Landlocked between India and China</li><li>Three ecological zones: Terai (plain), Hilly, Himalayan</li><li>Provinces: 7 | Districts: 77 | Local Bodies: 753</li><li>Highest peak: Mt. Everest (8,848.86m)</li><li>Major rivers: Koshi, Gandaki, Karnali (all flow south to Ganges)</li></ul><h3>Key Historical Events</h3><ul><li>1768: Prithvi Narayan Shah unifies Nepal</li><li>1816: Sugauli Treaty with British India</li><li>1951: End of Rana oligarchy, democracy established</li><li>1990: Multiparty democracy restored</li><li>2006: Peace Agreement, end of civil war</li><li>2008: Republic declared, monarchy abolished</li><li>2015: New Constitution promulgated</li></ul>",
                'content_np' => "<h2>नेपाल — भौगोलिक अवलोकन</h2><ul><li>क्षेत्रफल: १,४७,१८१ वर्ग कि.मी.</li><li>प्रदेश: ७ | जिल्ला: ७७ | स्थानीय तह: ७५३</li><li>सर्वोच्च शिखर: सगरमाथा (८,८४८.८६ मिटर)</li></ul>"],
            $base + ['display_order' => 3, 'read_time_minutes' => 7, 'title_en' => 'General Knowledge & Current Affairs', 'title_np' => 'सामान्य ज्ञान र समसामयिक',
                'summary_en' => 'National and international current affairs, important organizations, and general science.',
                'summary_np' => 'राष्ट्रिय र अन्तर्राष्ट्रिय समसामयिक, महत्त्वपूर्ण संगठन र सामान्य विज्ञान।',
                'content_en' => "<h2>Important Organizations</h2><ul><li><strong>UN:</strong> 193 members. Nepal joined 1955. HQ: New York.</li><li><strong>SAARC:</strong> 8 members. HQ: Kathmandu. Founded 1985.</li><li><strong>World Bank:</strong> HQ Washington DC. Provides development loans.</li><li><strong>IMF:</strong> International Monetary Fund. Financial stability.</li><li><strong>WHO:</strong> World Health Organization. HQ Geneva.</li></ul><h3>Nepal's National Symbols</h3><ul><li>National Bird: Danfe (Lophophorus)</li><li>National Flower: Rhododendron (Lali Gurans)</li><li>National Animal: Cow</li><li>National Sport: Volleyball</li><li>National Currency: Nepalese Rupee (NPR)</li></ul>",
                'content_np' => "<h2>महत्त्वपूर्ण संगठनहरू</h2><ul><li><strong>संयुक्त राष्ट्र:</strong> १९३ सदस्य। नेपाल १९५५ मा सामेल। मुख्यालय: न्युयोर्क।</li><li><strong>सार्क:</strong> ८ सदस्य। मुख्यालय: काठमाडौं। स्थापना १९८५।</li></ul>"],
            $base + ['display_order' => 4, 'read_time_minutes' => 9, 'title_en' => 'Maths & Reasoning for Loksewa', 'title_np' => 'लोकसेवाका लागि गणित र तर्क',
                'summary_en' => 'Arithmetic, percentages, ratios, simple interest, and logical reasoning patterns.',
                'summary_np' => 'अंकगणित, प्रतिशत, अनुपात, साधारण ब्याज र तार्किक तर्क।',
                'content_en' => "<h2>Key Math Topics for Loksewa</h2><h3>Percentage</h3><p>Formula: (Part/Whole) × 100. Example: 24 out of 80 = (24/80)×100 = 30%</p><h3>Simple Interest</h3><p>SI = (P × R × T) / 100. P=Principal, R=Rate, T=Time in years.</p><h3>Ratio & Proportion</h3><p>If A:B = 2:3 and total = 100, then A = 40, B = 60.</p><h3>Average</h3><p>Average = Sum of values / Number of values.</p><h3>Profit & Loss</h3><p>Profit% = (Profit/Cost Price) × 100. Loss% = (Loss/Cost Price) × 100.</p>",
                'content_np' => "<h2>लोकसेवाका लागि गणितका प्रमुख विषयहरू</h2><h3>प्रतिशत</h3><p>सूत्र: (भाग/जम्मा) × १००।</p><h3>साधारण ब्याज</h3><p>सा.ब्याज = (मूलधन × दर × समय) / १००।</p>"],
            $base + ['display_order' => 5, 'read_time_minutes' => 6, 'title_en' => 'Civil Service Structure in Nepal', 'title_np' => 'नेपालको निजामती सेवा संरचना',
                'summary_en' => 'Levels of civil service, Public Service Commission, and exam patterns.',
                'summary_np' => 'निजामती सेवाका तह, लोकसेवा आयोग र परीक्षाको ढाँचा।',
                'content_en' => "<h2>Nepal Civil Service Structure</h2><h3>Service Levels</h3><ul><li><strong>Gazetted Special Class:</strong> Secretary level</li><li><strong>Gazetted First Class:</strong> Joint Secretary</li><li><strong>Gazetted Second Class:</strong> Under Secretary</li><li><strong>Gazetted Third Class:</strong> Section Officer</li><li><strong>Non-Gazetted First Class:</strong> Nayab Subba</li><li><strong>Non-Gazetted Second Class:</strong> Assistant</li></ul><h3>PSC (Lok Sewa Aayog)</h3><p>Constitutional body. Conducts examinations for civil service. Exam stages: Written (objective + subjective) → Interview.</p>",
                'content_np' => "<h2>नेपाल निजामती सेवा संरचना</h2><ul><li>राजपत्रांकित विशेष: सचिव स्तर</li><li>राजपत्रांकित प्रथम: सहसचिव</li><li>राजपत्रांकित द्वितीय: उपसचिव</li><li>राजपत्रांकित तृतीय: शाखा अधिकृत</li></ul>"],
        ];
    }

    private function financeChapters($catId): array
    {
        if (!$catId) return [];
        $base = ['learning_category_id' => $catId, 'is_published' => true, 'published_at' => now()];
        return [
            $base + ['display_order' => 1, 'read_time_minutes' => 7, 'title_en' => 'Banking Basics in Nepal', 'title_np' => 'नेपालमा बैंकिङ आधारभूत ज्ञान',
                'summary_en' => 'Types of banks, accounts, deposits, loans, and interest rates in Nepal.',
                'summary_np' => 'नेपालमा बैंकका प्रकार, खाता, निक्षेप, ऋण र ब्याज दर।',
                'content_en' => "<h2>Banking in Nepal</h2><h3>Types of Banks (NRB Classification)</h3><ul><li><strong>Class A — Commercial Banks:</strong> 20 in Nepal (e.g. NBL, RBB, NABIL, NIC Asia)</li><li><strong>Class B — Development Banks:</strong> Provide agricultural/development loans</li><li><strong>Class C — Finance Companies:</strong> Hire purchase, leasing</li><li><strong>Class D — Micro Finance:</strong> Small loans to low-income groups</li></ul><h3>Types of Accounts</h3><ul><li><strong>Savings Account:</strong> For individuals. Earns interest. Can withdraw anytime.</li><li><strong>Current Account:</strong> For businesses. No interest. Unlimited transactions.</li><li><strong>Fixed Deposit:</strong> Locked for fixed term. Higher interest rate.</li></ul><h3>Key Interest Rates</h3><p>Nepal Rastra Bank sets the base rate. Banks cannot lend below base rate. Currently savings rate ~5-7%, FD ~8-10%, home loan ~10-12%.</p>",
                'content_np' => "<h2>नेपालमा बैंकिङ</h2><h3>बैंकका प्रकार (नेरास वर्गीकरण)</h3><ul><li><strong>वर्ग क — वाणिज्य बैंक:</strong> नेपालमा २०</li><li><strong>वर्ग ख — विकास बैंक</strong></li><li><strong>वर्ग ग — वित्त कम्पनी</strong></li><li><strong>वर्ग घ — लघुवित्त</strong></li></ul>"],
            $base + ['display_order' => 2, 'read_time_minutes' => 8, 'title_en' => 'Personal Savings & Investment', 'title_np' => 'व्यक्तिगत बचत र लगानी',
                'summary_en' => 'How to save money, invest in stocks, mutual funds, and manage personal finances.',
                'summary_np' => 'पैसा बचत गर्ने, शेयर, म्युचुअल फन्डमा लगानी गर्ने र व्यक्तिगत वित्त व्यवस्थापन।',
                'content_en' => "<h2>Personal Finance Management</h2><h3>50-30-20 Rule</h3><p>50% of income for needs, 30% for wants, 20% for savings/investments.</p><h3>Investment Options in Nepal</h3><ul><li><strong>Stock Market (NEPSE):</strong> Buy shares of listed companies. High risk, high return.</li><li><strong>Mutual Funds:</strong> Pooled investment. Lower risk. Managed by professionals.</li><li><strong>Fixed Deposit:</strong> Safe, guaranteed return.</li><li><strong>Real Estate:</strong> Land/property. High capital needed.</li><li><strong>Debentures/Bonds:</strong> Fixed income instruments issued by companies/government.</li></ul><h3>NEPSE (Nepal Stock Exchange)</h3><p>Established in 1994. Index called NEPSE Index. Trading days: Sunday to Thursday.</p>",
                'content_np' => "<h2>व्यक्तिगत वित्त व्यवस्थापन</h2><h3>५०-३०-२० नियम</h3><p>आयको ५०% आवश्यकताका लागि, ३०% इच्छाका लागि, २०% बचत/लगानीका लागि।</p>"],
            $base + ['display_order' => 3, 'read_time_minutes' => 6, 'title_en' => 'Insurance in Nepal', 'title_np' => 'नेपालमा बीमा',
                'summary_en' => 'Life insurance, health insurance, vehicle insurance — types, benefits, and claims.',
                'summary_np' => 'जीवन बीमा, स्वास्थ्य बीमा, सवारी बीमा — प्रकार, फाइदा र दाबी।',
                'content_en' => "<h2>Insurance in Nepal</h2><h3>Types of Insurance</h3><ul><li><strong>Life Insurance:</strong> Pays benefit on death or maturity. Premium paid regularly.</li><li><strong>Health Insurance:</strong> Covers medical expenses. Government provides Rs.5 lakh coverage to citizens.</li><li><strong>Vehicle Insurance:</strong> Mandatory for all vehicles. Third-party liability is compulsory.</li><li><strong>Property Insurance:</strong> Covers fire, theft, natural disaster damage.</li></ul><h3>Insurance Regulator</h3><p>Insurance Regulatory Authority of Nepal (IRAN) — formerly Beema Samiti — regulates all insurance companies.</p>",
                'content_np' => "<h2>नेपालमा बीमा</h2><ul><li><strong>जीवन बीमा:</strong> मृत्यु वा परिपक्वतामा लाभ प्रदान गर्छ।</li><li><strong>स्वास्थ्य बीमा:</strong> सरकारले नागरिकलाई ५ लाख रु. को कभरेज दिन्छ।</li><li><strong>सवारी बीमा:</strong> सबै सवारीका लागि अनिवार्य।</li></ul>"],
            $base + ['display_order' => 4, 'read_time_minutes' => 7, 'title_en' => 'Taxation in Nepal', 'title_np' => 'नेपालमा कर',
                'summary_en' => 'Income tax, VAT, property tax, and how to file returns in Nepal.',
                'summary_np' => 'आयकर, मूल्य अभिवृद्धि कर, घर जग्गा कर र रिटर्न भर्ने तरिका।',
                'content_en' => "<h2>Taxation in Nepal</h2><h3>Income Tax</h3><p>Individual income tax slabs (2080/81):</p><ul><li>Up to Rs. 5 lakh — 1%</li><li>Rs. 5-7 lakh — 10%</li><li>Rs. 7-20 lakh — 20%</li><li>Above Rs. 20 lakh — 30%</li></ul><h3>VAT (Value Added Tax)</h3><p>Standard rate: 13%. Applied on goods and services. Registered businesses collect and remit to IRD.</p><h3>PAN (Permanent Account Number)</h3><p>Mandatory for all taxpayers. Required for property transactions, business registration, bank accounts above threshold.</p>",
                'content_np' => "<h2>नेपालमा कर</h2><h3>आयकर स्ल्याब (२०८०/८१)</h3><ul><li>५ लाखसम्म — १%</li><li>५-७ लाख — १०%</li><li>७-२० लाख — २०%</li><li>२० लाखभन्दा माथि — ३०%</li></ul><h3>मूअकर</h3><p>मानक दर: १३%।</p>"],
        ];
    }

    private function rightsChapters($catId): array
    {
        if (!$catId) return [];
        $base = ['learning_category_id' => $catId, 'is_published' => true, 'published_at' => now()];
        return [
            $base + ['display_order' => 1, 'read_time_minutes' => 8, 'title_en' => 'Fundamental Rights of Citizens', 'title_np' => 'नागरिकका मौलिक हकहरू',
                'summary_en' => 'Constitutional fundamental rights: equality, freedom, education, health, and more.',
                'summary_np' => 'संवैधानिक मौलिक हक: समानता, स्वतन्त्रता, शिक्षा, स्वास्थ्य र अन्य।',
                'content_en' => "<h2>Fundamental Rights (Nepal Constitution 2072)</h2><p>Part 3 (Articles 16-46) of the Constitution guarantees 31 fundamental rights:</p><ul><li><strong>Right to Equality (Art. 18):</strong> No discrimination on basis of origin, religion, race, caste, sex, disability.</li><li><strong>Right to Freedom (Art. 17):</strong> Freedom of speech, assembly, association, movement, profession.</li><li><strong>Right to Education (Art. 31):</strong> Free basic education (up to class 8) is guaranteed.</li><li><strong>Right to Health (Art. 35):</strong> Basic health services free from state.</li><li><strong>Right to Food (Art. 36):</strong> No citizen shall be deprived of food.</li><li><strong>Right to Housing (Art. 37):</strong> No citizen shall be deprived of housing.</li><li><strong>Right to Property (Art. 25):</strong> Right to acquire, own, and sell property.</li></ul>",
                'content_np' => "<h2>मौलिक हकहरू (संविधान २०७२)</h2><p>संविधानको भाग ३ (धारा १६-४६) ले ३१ मौलिक हकहरू सुनिश्चित गर्छ।</p><ul><li><strong>समानताको हक (धारा १८)</strong></li><li><strong>स्वतन्त्रताको हक (धारा १७)</strong></li><li><strong>शिक्षाको हक (धारा ३१):</strong> कक्षा ८ सम्म निःशुल्क।</li><li><strong>स्वास्थ्यको हक (धारा ३५)</strong></li></ul>"],
            $base + ['display_order' => 2, 'read_time_minutes' => 7, 'title_en' => 'Labor Rights & Employment Law', 'title_np' => 'श्रम अधिकार र रोजगार कानुन',
                'summary_en' => 'Workers rights, minimum wage, working hours, leave entitlements under Nepal Labor Act 2074.',
                'summary_np' => 'श्रम ऐन २०७४ अन्तर्गत श्रमिकका अधिकार, न्यूनतम ज्याला, काम घण्टा र बिदा।',
                'content_en' => "<h2>Nepal Labor Act 2074 (2017)</h2><h3>Working Hours</h3><p>Maximum 8 hours per day, 48 hours per week. Overtime must be paid at 1.5x rate.</p><h3>Minimum Wage</h3><p>As of 2080 BS — Rs. 15,000/month for unskilled workers. Reviewed periodically by government.</p><h3>Leave Entitlements</h3><ul><li>Annual/Earned Leave: 1 day per 20 days worked</li><li>Sick Leave: 12 days per year</li><li>Maternity Leave: 14 weeks (98 days)</li><li>Paternity Leave: 15 days</li><li>Mourning Leave: 13 days</li></ul>",
                'content_np' => "<h2>श्रम ऐन २०७४</h2><h3>काम घण्टा</h3><p>दैनिक अधिकतम ८ घण्टा, साप्ताहिक ४८ घण्टा।</p><h3>न्यूनतम ज्याला</h3><p>२०८० बाट — अदक्ष श्रमिकका लागि रु. १५,०००/महिना।</p>"],
            $base + ['display_order' => 3, 'read_time_minutes' => 6, 'title_en' => 'Consumer Rights & Protection', 'title_np' => 'उपभोक्ता हक र संरक्षण',
                'summary_en' => 'Your rights as a consumer, how to file complaints, and consumer protection law in Nepal.',
                'summary_np' => 'उपभोक्ताका अधिकार, उजुरी गर्ने तरिका र नेपालमा उपभोक्ता संरक्षण कानुन।',
                'content_en' => "<h2>Consumer Rights in Nepal</h2><h3>Consumer Protection Act 2075</h3><p>Protects consumers from unfair trade practices, substandard goods, and fraud.</p><h3>Your Rights as a Consumer</h3><ul><li>Right to Safety — protection from hazardous goods</li><li>Right to Information — truthful labeling and advertising</li><li>Right to Choose — access to variety at competitive prices</li><li>Right to Redress — compensation for defective products</li><li>Right to be Heard — representation in policy decisions</li></ul><h3>How to File a Complaint</h3><p>Contact Department of Commerce, Supplies and Consumer Protection. File written complaint. Can also complain to District Administration Office.</p>",
                'content_np' => "<h2>नेपालमा उपभोक्ता हक</h2><h3>उपभोक्ता संरक्षण ऐन २०७५</h3><p>अनुचित व्यापारिक अभ्यास, निम्नस्तरीय सामान र ठगीबाट उपभोक्ताको संरक्षण।</p>"],
        ];
    }

    private function competitiveChapters($catId): array
    {
        if (!$catId) return [];
        $base = ['learning_category_id' => $catId, 'is_published' => true, 'published_at' => now()];
        return [
            $base + ['display_order' => 1, 'read_time_minutes' => 8, 'title_en' => 'Banking Exam Preparation', 'title_np' => 'बैंकिङ परीक्षा तयारी',
                'summary_en' => 'Topics covered in NRB, commercial bank, and development bank recruitment exams.',
                'summary_np' => 'नेरास, वाणिज्य बैंक र विकास बैंक भर्ती परीक्षामा समावेश विषयहरू।',
                'content_en' => "<h2>Banking Exam Syllabus Overview</h2><h3>Nepal Rastra Bank (NRB) Exams</h3><ul><li>General Banking Knowledge</li><li>Economics & Finance</li><li>Accounting principles</li><li>Legal provisions (Banking & Financial Institutions Act 2073)</li><li>IT in Banking</li></ul><h3>Commercial Bank Exams</h3><p>Typically cover: Aptitude (math, English, reasoning), Banking knowledge, Current affairs, Computer basics.</p><h3>BFIA 2073 Key Points</h3><ul><li>Minimum capital for commercial banks: Rs. 8 billion</li><li>CRR (Cash Reserve Ratio): Set by NRB</li><li>Single borrower limit: 25% of core capital</li></ul>",
                'content_np' => "<h2>बैंकिङ परीक्षाको पाठ्यक्रम</h2><p>बैंकिङ परीक्षाहरूमा: सामान्य बैंकिङ ज्ञान, अर्थशास्त्र, लेखा, कानुनी व्यवस्था र IT समावेश हुन्छ।</p>"],
            $base + ['display_order' => 2, 'read_time_minutes' => 7, 'title_en' => 'Teaching License (Shikshak Sewa)', 'title_np' => 'शिक्षक सेवा आयोग',
                'summary_en' => 'Teaching Service Commission exam structure, eligibility, and preparation strategy.',
                'summary_np' => 'शिक्षक सेवा आयोग परीक्षाको संरचना, योग्यता र तयारी रणनीति।',
                'content_en' => "<h2>Teaching Service Commission (TSC)</h2><h3>Exam Structure</h3><ul><li><strong>Lower Secondary (1-8):</strong> PCL/Diploma or equivalent. Exam: Written + Practical</li><li><strong>Secondary (9-12):</strong> Bachelor's degree in education or subject + BEd</li><li><strong>Higher Secondary:</strong> Master's degree</li></ul><h3>Written Exam Pattern</h3><ul><li>Paper 1: Subject Knowledge (100 marks)</li><li>Paper 2: Teaching Methodology + Education Law (100 marks)</li></ul><h3>Key Education Laws</h3><ul><li>Education Act 2028 (amended)</li><li>Compulsory and Free Education Act</li><li>Education Regulations</li></ul>",
                'content_np' => "<h2>शिक्षक सेवा आयोग</h2><h3>परीक्षाको संरचना</h3><ul><li>निम्न माध्यमिक: पी.सी.एल. वा सो सरह</li><li>माध्यमिक: स्नातक शिक्षा</li><li>उच्च माध्यमिक: स्नातकोत्तर</li></ul>"],
            $base + ['display_order' => 3, 'read_time_minutes' => 6, 'title_en' => 'Nepal Army & Police Recruitment', 'title_np' => 'नेपाल सेना र प्रहरी भर्ती',
                'summary_en' => 'Physical fitness requirements, written exam pattern for Nepal Army and Police.',
                'summary_np' => 'नेपाल सेना र प्रहरीका लागि शारीरिक फिटनेस आवश्यकता र लिखित परीक्षाको ढाँचा।',
                'content_en' => "<h2>Nepal Army Recruitment</h2><h3>Sainya (Private) Requirements</h3><ul><li>Age: 18-25 years</li><li>Education: SLC/SEE passed</li><li>Height: 5'3\" minimum (men), 5'1\" (women)</li><li>Chest: 30\" minimum (men)</li></ul><h3>Written Exam</h3><p>Covers: Nepali language, Math, General Knowledge, Social Studies.</p><h3>Nepal Police</h3><p>Constable requirements similar. Physical test: 1600m run, push-ups, sit-ups. Written exam after physical.</p>",
                'content_np' => "<h2>नेपाली सेना भर्ती</h2><h3>सैनिकका लागि आवश्यकता</h3><ul><li>उमेर: १८-२५ वर्ष</li><li>शिक्षा: एसईई उत्तीर्ण</li><li>उचाइ: न्यूनतम ५'३\" (पुरुष)</li></ul>"],
        ];
    }
}
