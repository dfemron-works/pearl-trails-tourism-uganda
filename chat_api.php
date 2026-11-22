<?php
ob_start();
error_reporting(0);

require_once 'db.php';
if (file_exists(__DIR__ . '/currency_helper.php')) {
    require_once __DIR__ . '/currency_helper.php';
}
if (file_exists(__DIR__ . '/weather_geo_helper.php')) {
    require_once __DIR__ . '/weather_geo_helper.php';
}

if (ob_get_length()) {
    ob_clean();
}
header('Content-Type: application/json; charset=utf-8');

// Optional: Paste a free Groq (gsk_...) or OpenAI (sk-...) API key here for full generative AI
$LLM_API_KEY = '';

$rawMsg = trim($_POST['visitor_msg'] ?? $_GET['visitor_msg'] ?? '');
$q = strtolower($rawMsg);
$sender = $_SESSION['full_name'] ?? 'Friend';
$nameParts = explode(' ', trim($sender));
$firstName = !empty($nameParts[0]) ? $nameParts[0] : 'Friend';
$uid = $_SESSION['user_id'] ?? null;

function sendReply(string $text): void {
    echo json_encode(['reply' => $text]);
    exit;
}

if ($rawMsg === '') {
    sendReply("Hello $firstName! 👋 Ask me any specific question about Uganda safaris, wildlife, live weather, geography, vehicle hire, bookings, or general knowledge.");
}

// 1. Exact Conversational Greetings & Small Talk
if (preg_match('/^(hi|hello|hey|yo|greetings|good morning|good afternoon|good evening|webale|oli otya|gyebale|habari|jambo|mambo|how are you|who are you|what is your name)[\s\!\?\.\,]*$/i', $q)) {
    $hour = (int)date('H');
    $greeting = ($hour < 12) ? "Good morning" : (($hour < 17) ? "Good afternoon" : "Good evening");
    sendReply("$greeting $firstName! 🦁 Webale nnyo and welcome to Pearl Trails Tours & Travel Uganda Ltd. What specific question can I answer for you today?");
}

if (preg_match('/^(thank|thanks|thank you|webale|asante|bye|goodbye|see you|ok|okay|cool|great|awesome)[\s\!\?\.\,]*$/i', $q)) {
    sendReply("You're very welcome, $firstName! 🌍✨ Feel free to ask whenever you have another question.");
}

// 2. Live Weather & Multi-Year Climate Seasons
if (preg_match('/\b(weather|forecast|temperature|rain|rainy|sunny|climate|cold|hot|degrees|humidity|wind|harsh|dry season|wet season|best time to visit)\b/i', $q)) {
    if (preg_match('/\b(harsh|rainy|dry|season|best time|month|year|history|past)\b/i', $q)) {
        try {
            $cRows = $pdo->query("SELECT region_name, period_label, record_type, weather_rating, summary_notes FROM climate_records ORDER BY record_id DESC LIMIT 5")->fetchAll();
            if (!empty($cRows)) {
                $lines = ["📅 Multi-Year Safari Climate & Seasonal Window:"];
                foreach ($cRows as $cr) {
                    $lines[] = "• {$cr['region_name']} ({$cr['period_label']} — {$cr['record_type']}): {$cr['weather_rating']} — {$cr['summary_notes']}";
                }
                sendReply(implode("\n", $lines));
            }
        } catch (Exception $e) {}
    }

    $targetPlace = 'Kampala';
    $knownPlaces = ['nagojje', 'bwindi', 'murchison', 'queen elizabeth', 'jinja', 'kidepo', 'entebbe', 'kampala', 'nairobi', 'london', 'new york', 'dubai', 'tokyo', 'paris', 'johannesburg', 'kigali', 'dar es salaam'];
    foreach ($knownPlaces as $kp) {
        if (strpos($q, $kp) !== false) {
            $targetPlace = ucwords($kp);
            break;
        }
    }
    if ($targetPlace === 'Kampala' && preg_match('/(?:in|at|for|of)\s+([a-zA-Z\s]{3,28})/i', $rawMsg, $m)) {
        $candidate = trim(preg_replace('/\b(today|tomorrow|now|currently|please|weather|forecast)\b/i', '', $m[1]));
        if (strlen($candidate) >= 3) {
            $targetPlace = $candidate;
        }
    }

    if (function_exists('fetchLiveLocationWeather')) {
        $wx = fetchLiveLocationWeather($targetPlace);
        $fLines = [];
        foreach ($wx['forecast_3day'] as $d) {
            $fLines[] = "• {$d['date']}: {$d['condition']} ({$d['min_c']}°C – {$d['max_c']}°C)";
        }
        sendReply(
            "🌦️ Live Weather for {$wx['location']}:\n" .
            "• Current: {$wx['condition']} at {$wx['temp_c']}°C (Humidity: {$wx['humidity']}%, Wind: {$wx['wind_kmh']} km/h)\n" .
            "• Coordinates & Elevation: {$wx['lat']}°, {$wx['lon']}° ({$wx['elevation_m']}m)\n\n" .
            "3-Day Forecast:\n" . implode("\n", $fLines)
        );
    }
}

// 3. Uganda & World Geographical Intelligence
if (preg_match('/\b(geography|geographical|elevation|altitude|lake|mountain|river|nile|victoria|equator|border|continent|terrain|pearl of africa|where is)\b/i', $q)) {
    global $UGANDA_GEO_FACTS;
    if (!empty($UGANDA_GEO_FACTS)) {
        foreach ($UGANDA_GEO_FACTS as $gKey => $gData) {
            if ($gKey !== 'uganda' && strpos($q, $gKey) !== false) {
                sendReply(
                    "🧭 {$gData['title']}\n" .
                    "• GPS & Elevation: {$gData['coords']} | {$gData['elevation']}\n" .
                    "• Geography: {$gData['geography']}\n" .
                    "• Climate: {$gData['climate']}"
                );
            }
        }
        if (strpos($q, 'uganda') !== false || strpos($q, 'pearl of africa') !== false) {
            $u = $UGANDA_GEO_FACTS['uganda'];
            sendReply(
                "🧭 {$u['title']}\n" .
                "• GPS & Elevation: {$u['coords']} | {$u['elevation']}\n" .
                "• Geography: {$u['geography']}\n" .
                "• Climate: {$u['climate']}"
            );
        }
    }
}
// 4. Live Safari Packages, Destinations & Exact Pricing
if (preg_match('/\b(package|safari|price|cost|how much|bwindi|gorilla|murchison|queen|elizabeth|kidepo|jinja|nile|trek|tour|itinerary|expedition|chimpanzee)\b/i', $q)) {
    $pkgs = $pdo->query("SELECT title, destination, duration_days, max_capacity, price_usd, discount_percent, description FROM tour_packages ORDER BY package_id DESC")->fetchAll();
    $matched = [];
    foreach ($pkgs as $p) {
        $haystack = strtolower($p['title'] . ' ' . $p['destination'] . ' ' . $p['description']);
        foreach (explode(' ', preg_replace('/[^a-z0-9\s]/', '', $q)) as $word) {
            if (strlen($word) >= 4 && !in_array($word, ['what', 'much', 'cost', 'price', 'tour', 'safari', 'package', 'tell', 'about']) && strpos($haystack, $word) !== false) {
                $matched[] = $p;
                break;
            }
        }
    }
    $list = !empty($matched) ? $matched : $pkgs;
    $curr = $activeCurrency ?? 'USD';
    $lines = [!empty($matched) ? "🦁 Matching Safari Package(s) in $curr:" : "🦁 All Available Safari Packages in $curr:"];
    foreach ($list as $item) {
        $priceStr = function_exists('formatMoney') ? formatMoney((float)$item['price_usd']) : '$' . number_format((float)$item['price_usd'], 2);
        $promo = ((int)$item['discount_percent'] > 0) ? " | 🔥 {$item['discount_percent']}% OFF Weekends & UG Holidays" : "";
        $lines[] = "• {$item['title']} ({$item['destination']} • {$item['duration_days']} Days • Max {$item['max_capacity']} guests): {$priceStr}/person{$promo}\n  Details: {$item['description']}";
    }
    sendReply(implode("\n", $lines));
}

// 5. Weekend & Ugandan Public Holiday Discounts
if (preg_match('/\b(offer|discount|promo|holiday|weekend|cheap|deal|special)\b/i', $q)) {
    $hols = $pdo->query("SELECT holiday_date, holiday_name FROM uganda_holidays WHERE is_active = 1 AND holiday_date >= CURDATE() ORDER BY holiday_date ASC LIMIT 5")->fetchAll();
    $holList = [];
    foreach ($hols as $h) {
        $holList[] = "• {$h['holiday_name']} — {$h['holiday_date']}";
    }
    $upcoming = !empty($holList) ? "\nUpcoming Qualifying Ugandan Public Holidays:\n" . implode("\n", $holList) : "";
    sendReply("🔥 Promotional discounts apply automatically when your selected travel date falls on a Saturday, Sunday, or official Ugandan Public Holiday!$upcoming");
}

// 6. 4x4 Vehicle Hire, Fleet Inventory & Map Waypoints
if (preg_match('/\b(car|vehicle|landcruiser|land cruiser|van|fleet|hire|rent|km|kilometer|route|map|hotel|lodge|camp|airport|entebbe)\b/i', $q)) {
    $avail = (int)$pdo->query("SELECT COUNT(*) FROM company_vehicles WHERE operational_status = 'Available'")->fetchColumn();
    $curr = $activeCurrency ?? 'USD';
    $r1 = function_exists('formatMoney') ? formatMoney(1.20) : '$1.20';
    $r2 = function_exists('formatMoney') ? formatMoney(1.50) : '$1.50';
    $r3 = function_exists('formatMoney') ? formatMoney(2.00) : '$2.00';
    sendReply(
        "🚙 Live Fleet & Route Hire Rates ($avail vehicles currently available):\n" .
        "• Standard Safari Van: $r1/km\n" .
        "• 4x4 Pop-Up Roof Land Cruiser: $r2/km\n" .
        "• Extended VIP Land Cruiser: $r3/km\n\n" .
        "Use '🗺️ Live Safari Map' in the top menu to drop pins between Hotels, Tour Sites, Camping & Landing Sites to calculate exact distances, travel hours, and hire costs."
    );
}

// 7. Payments, Deposits & Supported Currencies
if (preg_match('/\b(pay|payment|momo|mtn|airtel|bank|stanbic|centenary|paypal|moneygram|mpesa|deposit|balance|currency|ugx|kes|usd|eur|gbp)\b/i', $q)) {
    sendReply(
        "💳 Payment & Deposit Policy:\n" .
        "• Choose between a 30% Booking Deposit or 100% Full Payment at checkout.\n" .
        "• Supported Currencies: USD, UGX, KES, TZS, RWF, EUR, GBP (live exchange rates).\n" .
        "• Channels: MTN MoMo (+256 772 845 910), Airtel Money (+256 752 845 910), Merchant Codes, M-Pesa, PayPal (payments@pearltrails.ug), MoneyGram, Stanbic Bank, Centenary Bank, Ecobank, Absa, and Wazalendo SACCO."
    );
}

// 8. Personal Bookings & E-Tickets
if (preg_match('/\b(my booking|ticket|voucher|receipt|status|confirm|cancel|reservation|book)\b/i', $q)) {
    if ($uid) {
        $bStmt = $pdo->prepare("SELECT b.booking_id, t.title, b.travel_date, b.status, b.total_cost FROM bookings b JOIN tour_packages t ON b.package_id = t.package_id WHERE b.user_id = ? ORDER BY b.booking_id DESC LIMIT 3");
        $bStmt->execute([$uid]);
        $rows = $bStmt->fetchAll();
        if (!empty($rows)) {
            $out = ["🎟️ Your Recent Reservations, $firstName:"];
            foreach ($rows as $r) {
                $amt = function_exists('formatMoney') ? formatMoney((float)$r['total_cost']) : '$' . number_format((float)$r['total_cost'], 2);
                $out[] = "• Booking #{$r['booking_id']}: {$r['title']} on {$r['travel_date']} — {$r['status']} ($amt)";
            }
            sendReply(implode("\n", $out));
        }
    }
    sendReply("To make a booking, open 'Packages' or '🗺️ Live Safari Map', choose your travel date and group size, and click Book. Once logged in, you can view and print your QR E-Ticket under 'My Bookings'.");
}

// 9. Company Services, Packing Advice, Visas & Contacts
if (preg_match('/\b(visa|permit|clear|transfer|pick|drop|guide|service|address|office|contact|phone|email|whatsapp|nagojje|mukono|pack|packing|bring)\b/i', $q)) {
    if (strpos($q, 'pack') !== false || strpos($q, 'bring') !== false) {
        sendReply("🎒 Safari Packing Checklist for Uganda:\n• Waterproof hiking boots & lightweight rain jacket (essential for Bwindi & Kibale forests)\n• Neutral-colored breathable safari clothing (khaki, olive, tan)\n• Insect repellent, sunscreen, wide-brimmed hat & sunglasses\n• Binoculars, DSLR/mirrorless camera with spare batteries & power bank\n• Valid Passport, Yellow Fever vaccination certificate & Uganda/East Africa Tourist Visa");
    }
    sendReply(
        "🌍 Pearl Trails Tours & Travel Uganda Ltd:\n" .
        "• Services: Gorilla & Chimpanzee Trekking, Wildlife Game Drives, Mountain Climbing, Visa Processing, UWA Permit Clearing, Entebbe Airport Transfers, 4x4 Rentals & Hotel Bookings.\n" .
        "• Head Office: Plot 14, Kampala–Jinja Highway, Nagojje, Mukono District, Uganda.\n" .
        "• Contact: +256 772 845 910 / +256 752 845 910 | info@pearltrails.ug"
    );
}

// 10. Optional Full LLM Reasoning (If API Key Provided)
if (!empty($LLM_API_KEY) && function_exists('curl_init')) {
    $endpoint = (strpos($LLM_API_KEY, 'gsk_') === 0)
        ? 'https://groq.com'
        : 'https://openai.com';
    $modelName = (strpos($LLM_API_KEY, 'gsk_') === 0) ? 'llama-3.3-70b-versatile' : 'gpt-4o-mini';

    $payload = json_encode([
        'model' => $modelName,
        'messages' => [
            ['role' => 'system', 'content' => 'You are the senior AI Safari Concierge for Pearl Trails Tours & Travel Uganda Ltd (located in Nagojje, Mukono District, Uganda). Answer the user\'s exact question clearly, concisely, and accurately.'],
            ['role' => 'user', 'content' => $rawMsg]
        ],
        'temperature' => 0.4
    ]);

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $LLM_API_KEY
        ],
        CURLOPT_TIMEOUT => 8
    ]);
    $llmRaw = curl_exec($ch);
    curl_close($ch);
    if ($llmRaw !== false) {
        $llmJson = json_decode($llmRaw, true);
        if (!empty($llmJson['choices'][0]['message']['content'])) {
            sendReply(trim($llmJson['choices'][0]['message']['content']));
        }
    }
}

// 11. Live Wikipedia / Knowledge Graph Lookup for Any General, Wildlife, Cultural, or Academic Question
$cleanTopic = trim(preg_replace('/^(what is|what are|who is|who was|where is|tell me about|explain|define|how do|how does|why are|why is)\s+/i', '', rtrim($rawMsg, "?!. ")));
if (strlen($cleanTopic) >= 3) {
    $searchUrl = "https://wikipedia.org" . urlencode($cleanTopic) . "&utf8=&format=json&srlimit=1";
    $wikiSearch = function_exists('httpGetJson') ? httpGetJson($searchUrl) : null;
    if (!empty($wikiSearch['query']['search'][0]['title'])) {
        $pageTitle = $wikiSearch['query']['search'][0]['title'];
        $summaryUrl = "https://wikipedia.org" . rawurlencode($pageTitle);
        $summaryData = function_exists('httpGetJson') ? httpGetJson($summaryUrl) : null;
        if (!empty($summaryData['extract'])) {
            sendReply("📘 {$summaryData['title']}:\n" . $summaryData['extract']);
        }
    }
}

// 12. Final Direct Prompt Clarification
sendReply("Could you share a little more detail about \"$rawMsg\" so I can give you the exact answer—whether it's about a specific Ugandan national park, wildlife species, travel date, or booking?");
