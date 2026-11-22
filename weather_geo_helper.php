<?php
$UGANDA_GEO_FACTS = [
    'uganda' => [
        'title' => 'Republic of Uganda (The Pearl of Africa)',
        'coords' => '1.3733° N, 32.2903° E',
        'elevation' => '1,100m average (Lake Albert 621m to Margherita Peak 5,109m)',
        'climate' => 'Equatorial tropical climate moderated by altitude; two dry seasons (Dec–Feb, Jun–Aug) and two wet seasons (Mar–May, Sep–Nov).',
        'geography' => 'Landlocked East African nation bordered by Kenya, Tanzania, Rwanda, DR Congo, and South Sudan. Home to Lake Victoria, the Source of the River Nile, Rwenzori Mountains, and 10 National Parks.'
    ],
    'nagojje' => [
        'title' => 'Nagojje, Mukono District (Pearl Trails Head Office)',
        'coords' => '0.4167° N, 32.8833° E',
        'elevation' => '1,180m above sea level',
        'climate' => 'Warm equatorial forest-savannah transition (20°C–28°C year-round).',
        'geography' => 'Located along the Kampala–Jinja Highway in Central Uganda near Mabira Central Forest Reserve and Lake Victoria.'
    ],
    'bwindi' => [
        'title' => 'Bwindi Impenetrable National Park',
        'coords' => '1.0451° S, 29.6602° E',
        'elevation' => '1,160m – 2,607m',
        'climate' => 'Cool, misty montane rainforest (11°C–23°C); afternoon showers are common year-round.',
        'geography' => 'UNESCO World Heritage Site in Southwestern Uganda sheltering nearly half of the world\'s remaining endangered Mountain Gorillas.'
    ],
    'murchison' => [
        'title' => 'Murchison Falls National Park',
        'coords' => '2.2775° N, 31.6861° E',
        'elevation' => '619m – 1,292m',
        'climate' => 'Hot, dry northern savannah (22°C–34°C).',
        'geography' => 'Uganda\'s largest national park (3,893 km²), bisected by the Victoria Nile.'
    ],
    'queen elizabeth' => [
        'title' => 'Queen Elizabeth National Park',
        'coords' => '0.1917° S, 30.0881° E',
        'elevation' => '910m – 1,350m',
        'climate' => 'Warm savannah and crater-lake microclimate (18°C–29°C).',
        'geography' => 'Famous for volcanic craters, Kazinga Channel, and Ishasha tree-climbing lions.'
    ],
    'jinja' => [
        'title' => 'Jinja — Source of the River Nile',
        'coords' => '0.4479° N, 33.2026° E',
        'elevation' => '1,143m',
        'climate' => 'Pleasant lakeside tropical climate (19°C–28°C).',
        'geography' => 'Where the White Nile begins its northward journey from Lake Victoria.'
    ],
    'kidepo' => [
        'title' => 'Kidepo Valley National Park',
        'coords' => '3.7433° N, 33.7606° E',
        'elevation' => '914m – 2,750m',
        'climate' => 'Semi-arid rugged savannah (20°C–35°C).',
        'geography' => 'Remote Karamoja wilderness in Northeastern Uganda bordering South Sudan and Kenya.'
    ]
];

function decodeWeatherCode(int $code): string {
    $map = [
        0 => '☀️ Clear Sky', 1 => '🌤️ Mainly Clear', 2 => '⛅ Partly Cloudy',
        3 => '☁️ Overcast', 45 => '🌫️ Mist / Fog', 51 => '🌦️ Light Drizzle',
        61 => '🌧️ Light Rain', 63 => '🌧️ Moderate Rain', 65 => '⛈️ Heavy Rain',
        80 => '🌦️ Safari Showers', 95 => '⛈️ Thunderstorm'
    ];
    return $map[$code] ?? '🌤️ Fair Sky';
}

function httpGetJson(string $url): ?array {
    // Try cURL first (handles HTTPS/SSL reliably on XAMPP), then file_get_contents
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'PearlTrailsUganda/1.0'
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);
        if ($resp !== false) {
            $decoded = json_decode($resp, true);
            if (is_array($decoded)) return $decoded;
        }
    }
    $ctx = stream_context_create(['http' => ['timeout' => 4], 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
    $raw = @file_get_contents($url, false, $ctx);
    return ($raw !== false) ? json_decode($raw, true) : null;
}

function fetchLiveLocationWeather(string $placeQuery): array {
    $cleanPlace = trim($placeQuery) !== '' ? trim($placeQuery) : 'Kampala';

    // Built-in Coordinates & Distinct Regional Climate Profiles for Uganda + Global Hubs
    $presetCities = [
        'nagojje'         => ['name' => 'Nagojje, Mukono (Uganda)', 'lat' => 0.4167, 'lon' => 32.8833, 'elev' => 1180, 'temp' => 25.8, 'hum' => 70, 'wind' => 11.2, 'cond' => '⛅ Partly Cloudy'],
        'kampala'         => ['name' => 'Kampala (Uganda)', 'lat' => 0.3476, 'lon' => 32.5825, 'elev' => 1190, 'temp' => 26.4, 'hum' => 68, 'wind' => 12.0, 'cond' => '🌤️ Mainly Clear'],
        'entebbe'         => ['name' => 'Entebbe (Uganda)', 'lat' => 0.0512, 'lon' => 32.4637, 'elev' => 1140, 'temp' => 25.2, 'hum' => 76, 'wind' => 15.4, 'cond' => '🌦️ Lake Breeze Showers'],
        'bwindi'          => ['name' => 'Bwindi Impenetrable NP (Uganda)', 'lat' => -1.0451, 'lon' => 29.6602, 'elev' => 2100, 'temp' => 18.4, 'hum' => 84, 'wind' => 8.5, 'cond' => '🌫️ Misty Forest Canopy'],
        'murchison'       => ['name' => 'Murchison Falls NP (Uganda)', 'lat' => 2.2775, 'lon' => 31.6861, 'elev' => 750, 'temp' => 31.2, 'hum' => 54, 'wind' => 14.0, 'cond' => '☀️ Sunny Savanna'],
        'queen elizabeth' => ['name' => 'Queen Elizabeth NP (Uganda)', 'lat' => -0.1917, 'lon' => 30.0881, 'elev' => 980, 'temp' => 27.6, 'hum' => 62, 'wind' => 12.8, 'cond' => '⛅ Warm Rift Valley'],
        'jinja'           => ['name' => 'Jinja - Source of the Nile (Uganda)', 'lat' => 0.4479, 'lon' => 33.2026, 'elev' => 1143, 'temp' => 26.1, 'hum' => 71, 'wind' => 13.5, 'cond' => '🌤️ Nile Breeze'],
        'kidepo'          => ['name' => 'Kidepo Valley NP (Uganda)', 'lat' => 3.7433, 'lon' => 33.7606, 'elev' => 1200, 'temp' => 32.5, 'hum' => 45, 'wind' => 16.2, 'cond' => '☀️ Hot Arid Savanna'],
        'nairobi'         => ['name' => 'Nairobi (Kenya)', 'lat' => -1.2921, 'lon' => 36.8219, 'elev' => 1795, 'temp' => 22.8, 'hum' => 64, 'wind' => 14.6, 'cond' => '⛅ Cool Highland Breeze'],
        'kigali'          => ['name' => 'Kigali (Rwanda)', 'lat' => -1.9441, 'lon' => 30.0619, 'elev' => 1567, 'temp' => 23.5, 'hum' => 67, 'wind' => 10.4, 'cond' => '🌤️ Pleasant Hills'],
        'dar es salaam'   => ['name' => 'Dar es Salaam (Tanzania)', 'lat' => -6.7924, 'lon' => 39.2083, 'elev' => 14, 'temp' => 30.4, 'hum' => 78, 'wind' => 18.0, 'cond' => '☀️ Coastal Tropical'],
        'london'          => ['name' => 'London (United Kingdom)', 'lat' => 51.5072, 'lon' => -0.1276, 'elev' => 35, 'temp' => 9.2, 'hum' => 81, 'wind' => 19.5, 'cond' => '🌧️ Cool & Overcast'],
        'new york'        => ['name' => 'New York (United States)', 'lat' => 40.7128, 'lon' => -74.0060, 'elev' => 10, 'temp' => 7.8, 'hum' => 63, 'wind' => 17.2, 'cond' => '⛅ Crisp Autumn Sky'],
        'dubai'           => ['name' => 'Dubai (United Arab Emirates)', 'lat' => 25.2048, 'lon' => 55.2708, 'elev' => 5, 'temp' => 29.6, 'hum' => 48, 'wind' => 13.8, 'cond' => '☀️ Clear Desert Sun'],
        'tokyo'           => ['name' => 'Tokyo (Japan)', 'lat' => 35.6762, 'lon' => 139.6503, 'elev' => 40, 'temp' => 13.4, 'hum' => 59, 'wind' => 12.1, 'cond' => '🌤️ Clear & Mild'],
        'johannesburg'    => ['name' => 'Johannesburg (South Africa)', 'lat' => -26.2041, 'lon' => 28.0473, 'elev' => 1753, 'temp' => 24.1, 'hum' => 55, 'wind' => 15.0, 'cond' => '⛅ Highveld Sun'],
        'paris'           => ['name' => 'Paris (France)', 'lat' => 48.8566, 'lon' => 2.3522, 'elev' => 35, 'temp' => 10.1, 'hum' => 79, 'wind' => 16.0, 'cond' => '☁️ Cloudy & Cool']
    ];

    $key = strtolower($cleanPlace);
    $loc = $presetCities[$key] ?? null;

    if (!$loc) {
        $geoJson = httpGetJson("https://open-meteo.com" . urlencode($cleanPlace) . "&count=1&language=en&format=json");
        if (!empty($geoJson['results'])) {
            $r = $geoJson['results'];
            $loc = [
                'name' => $r['name'] . (!empty($r['country']) ? " ({$r['country']})" : ''),
                'lat' => (float)$r['latitude'],
                'lon' => (float)$r['longitude'],
                'elev' => (int)($r['elevation'] ?? 0),
                'temp' => 24.0, 'hum' => 65, 'wind' => 12.0, 'cond' => '🌤️ Fair Sky'
            ];
        }
    }

    if (!$loc) {
        $loc = [
            'name' => ucwords($cleanPlace),
            'lat' => 0.3476, 'lon' => 32.5825, 'elev' => 1190,
            'temp' => 25.0, 'hum' => 65, 'wind' => 12.0, 'cond' => '🌤️ Fair Sky'
        ];
    }

    // Attempt Live Satellite Weather Fetch for Exact Coordinates
    $wx = httpGetJson("https://open-meteo.com{$loc['lat']}&longitude={$loc['lon']}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto&forecast_days=3");
    if (!empty($wx['current'])) {
        $cur = $wx['current'];
        $daily = [];
        if (!empty($wx['daily']['time'])) {
            for ($i = 0; $i < count($wx['daily']['time']); $i++) {
                $daily[] = [
                    'date' => $wx['daily']['time'][$i],
                    'condition' => decodeWeatherCode((int)$wx['daily']['weather_code'][$i]),
                    'max_c' => round((float)$wx['daily']['temperature_2m_max'][$i], 1),
                    'min_c' => round((float)$wx['daily']['temperature_2m_min'][$i], 1),
                ];
            }
        }
        return [
            'source' => 'LIVE_SATELLITE',
            'location' => $loc['name'],
            'lat' => $loc['lat'],
            'lon' => $loc['lon'],
            'elevation_m' => $loc['elev'],
            'temp_c' => round((float)$cur['temperature_2m'], 1),
            'humidity' => (int)$cur['relative_humidity_2m'],
            'wind_kmh' => round((float)$cur['wind_speed_10m'], 1),
            'condition' => decodeWeatherCode((int)$cur['weather_code']),
            'forecast_3day' => $daily
        ];
    }

    // Distinct Regional Baseline Fallback (Never overwrites city name with Kampala)
    return [
        'source' => 'REGIONAL_MODEL',
        'location' => $loc['name'],
        'lat' => $loc['lat'],
        'lon' => $loc['lon'],
        'elevation_m' => $loc['elev'],
        'temp_c' => $loc['temp'],
        'humidity' => $loc['hum'],
        'wind_kmh' => $loc['wind'],
        'condition' => $loc['cond'],
        'forecast_3day' => [
            ['date' => date('Y-m-d'), 'condition' => $loc['cond'], 'max_c' => round($loc['temp'] + 2.0, 1), 'min_c' => round($loc['temp'] - 4.5, 1)],
            ['date' => date('Y-m-d', strtotime('+1 day')), 'condition' => '🌤️ Mainly Clear', 'max_c' => round($loc['temp'] + 2.5, 1), 'min_c' => round($loc['temp'] - 4.0, 1)],
            ['date' => date('Y-m-d', strtotime('+2 days')), 'condition' => '⛅ Partly Cloudy', 'max_c' => round($loc['temp'] + 1.0, 1), 'min_c' => round($loc['temp'] - 5.0, 1)],
        ]
    ];
}

function fetchWeatherForDateAndCoords(float $lat, float $lon, string $targetDate): string {
    $daysAhead = (int)floor((strtotime($targetDate) - time()) / 86400);
    if ($daysAhead >= 0 && $daysAhead <= 15) {
        $data = httpGetJson("https://open-meteo.com{$lat}&longitude={$lon}&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto&start_date={$targetDate}&end_date={$targetDate}");
        if (!empty($data['daily']['weather_code'][0])) {
            $cond = decodeWeatherCode((int)$data['daily']['weather_code'][0]);
            $maxC = round((float)$data['daily']['temperature_2m_max'][0], 1);
            $minC = round((float)$data['daily']['temperature_2m_min'][0], 1);
            return "{$cond} ({$minC}°C – {$maxC}°C) [Live Satellite Forecast]";
        }
    }

    $month = (int)date('n', strtotime($targetDate));
    if (in_array($month, [12, 1, 2, 6, 7, 8], true)) {
        return "☀️ Dry Safari Season (22°C – 30°C) — Good Weather & Clear Trails";
    } elseif (in_array($month, [3, 4, 5], true)) {
        return "🌧️ Long Rainy Season (17°C – 24°C) — Lush Scenery, 4x4 Recommended";
    } else {
        return "🌦️ Short Tropical Showers (19°C – 26°C) — Moderate Safari Conditions";
    }
}
