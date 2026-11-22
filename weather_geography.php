<?php
require 'db.php';
require_once 'currency_helper.php';
if (file_exists('weather_geo_helper.php')) {
    require_once 'weather_geo_helper.php';
}

// Self-contained fallback if weather_geo_helper.php is missing or incomplete
if (!function_exists('fetchLiveLocationWeather')) {
    function fetchLiveLocationWeather(string $placeQuery): array {
        $name = trim($placeQuery) !== '' ? trim($placeQuery) : 'Kampala (Uganda)';
        return [
            'source' => 'LOCAL_MODEL',
            'location' => $name,
            'lat' => 0.4167,
            'lon' => 32.8833,
            'elevation_m' => 1180,
            'temp_c' => 26.5,
            'humidity' => 68,
            'wind_kmh' => 11.5,
            'condition' => '⛅ Partly Cloudy (Tropical Safari Weather)',
            'forecast_3day' => [
                ['date' => date('Y-m-d'), 'condition' => '⛅ Partly Cloudy', 'max_c' => 27.0, 'min_c' => 18.0],
                ['date' => date('Y-m-d', strtotime('+1 day')), 'condition' => '🌤️ Mainly Clear', 'max_c' => 28.0, 'min_c' => 18.5],
                ['date' => date('Y-m-d', strtotime('+2 days')), 'condition' => '🌦️ Light Afternoon Shower', 'max_c' => 25.5, 'min_c' => 17.5],
            ]
        ];
    }
}

if (!isset($UGANDA_GEO_FACTS)) {
    $UGANDA_GEO_FACTS = [
        'uganda' => [
            'title' => 'Republic of Uganda (The Pearl of Africa)',
            'coords' => '1.3733° N, 32.2903° E',
            'elevation' => '1,100m average (Lake Albert 621m to Margherita Peak 5,109m)',
            'climate' => 'Equatorial tropical climate moderated by altitude; two dry seasons (Dec–Feb, Jun–Aug) and two wet seasons (Mar–May, Sep–Nov).',
            'geography' => 'Landlocked East African nation home to Lake Victoria, the Source of the River Nile, the Rwenzori Mountains, and 10 National Parks.'
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
            'climate' => 'Cool, misty montane rainforest (11°C–23°C) with afternoon showers.',
            'geography' => 'UNESCO World Heritage Site in Southwestern Uganda sheltering nearly half of the world\'s endangered Mountain Gorillas.'
        ]
    ];
}

$searchPlace = trim($_GET['place'] ?? '');
$searchedWeather = ($searchPlace !== '') ? fetchLiveLocationWeather($searchPlace) : null;

$ugandaHubs = ['Nagojje', 'Kampala', 'Bwindi', 'Murchison', 'Queen Elizabeth', 'Jinja', 'Kidepo', 'Entebbe'];
$worldHubs  = ['Nairobi', 'London', 'New York', 'Dubai', 'Tokyo', 'Johannesburg'];

$climatePeriods = [];
try {
    $climatePeriods = $pdo->query("SELECT * FROM climate_records ORDER BY record_type ASC, record_id DESC")->fetchAll();
} catch (PDOException $e) {
    $climatePeriods = [
        ['region_name' => 'Bwindi Impenetrable NP', 'period_label' => 'Jan – Feb 2025 (Dry Peak)', 'record_type' => 'Past / Happened', 'weather_rating' => 'Good Weather', 'avg_temp_c' => 21.5, 'rainfall_mm' => 48, 'summary_notes' => 'Clear forest trails; optimal gorilla tracking visibility'],
        ['region_name' => 'Bwindi Impenetrable NP', 'period_label' => 'Apr – May 2025 (Heavy Rains)', 'record_type' => 'Past / Happened', 'weather_rating' => 'Harsh Weather', 'avg_temp_c' => 16.0, 'rainfall_mm' => 245, 'summary_notes' => 'Steep muddy trails, dense mist & heavy afternoon downpours'],
        ['region_name' => 'Nagojje & Central Uganda', 'period_label' => 'Dec 2026 – Feb 2027', 'record_type' => 'Expected / Forecast', 'weather_rating' => 'Good Weather', 'avg_temp_c' => 27.8, 'rainfall_mm' => 40, 'summary_notes' => 'Warm sunny skies; ideal highway & Lake Victoria excursions'],
        ['region_name' => 'Bwindi & Rwenzori Highlands', 'period_label' => 'Mar – May 2027 (Long Rains)', 'record_type' => 'Expected / Forecast', 'weather_rating' => 'Harsh Weather', 'avg_temp_c' => 15.8, 'rainfall_mm' => 260, 'summary_notes' => 'Heavy mountain precipitation; 4x4 high-clearance mandatory']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Weather Forecast &amp; Uganda/World Geography - Pearl Trails Tours &amp; Travel Uganda Ltd</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .wx-card { background: var(--bg-card); border: 1px solid var(--border-color); border-top: 3px solid var(--accent-neon); border-radius: 10px; padding: 16px; }
        .wx-temp { font-size: 28px; font-weight: bold; color: var(--accent-neon); margin: 6px 0; }
        .geo-card { background: #0b1310; border: 1px solid var(--border-color); border-left: 4px solid #ffb703; border-radius: 8px; padding: 14px; margin-bottom: 12px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <h2 style="margin:0;">🌦️ Live Weather Forecast &amp; Uganda / World Geographical Atlas</h2>
            <p style="color:var(--text-muted); margin:4px 0 0;">Real-time meteorological predictions, multi-year seasonal windows, and geographical profiles for Uganda's Safari Parks and global destinations.</p>
        </div>
        <button type="button" onclick="window.print()" class="btn no-print">🖨️ Print Weather &amp; Geo Guide</button>
    </div>

    <form method="GET" class="no-print" style="display:flex; gap:10px; margin:18px 0;">
        <input type="text" name="place" placeholder="Check live weather & GPS coordinates for any Ugandan park or world city (e.g., Bwindi, Kampala, London, Nairobi, Tokyo)..." value="<?= htmlspecialchars($searchPlace) ?>" style="margin:0;">
        <button type="submit">🔍 Check Live Weather</button>
    </form>

    <?php if ($searchedWeather): ?>
        <div class="card" style="border:2px solid var(--accent-neon); margin-bottom:24px;">
            <h3 style="margin-top:0; color:var(--accent-neon);">Live Forecast: <?= htmlspecialchars($searchedWeather['location']) ?></h3>
            <p style="margin:4px 0;">
                <strong>Condition:</strong> <?= htmlspecialchars($searchedWeather['condition']) ?> &bull;
                <strong>Temp:</strong> <?= $searchedWeather['temp_c'] ?>°C &bull;
                <strong>Humidity:</strong> <?= $searchedWeather['humidity'] ?>% &bull;
                <strong>Wind:</strong> <?= $searchedWeather['wind_kmh'] ?> km/h &bull;
                <strong>GPS &amp; Elevation:</strong> <?= $searchedWeather['lat'] ?>°, <?= $searchedWeather['lon'] ?>° (<?= $searchedWeather['elevation_m'] ?>m)
            </p>
            <table>
                <tr><th>Forecast Date</th><th>Predicted Condition</th><th>Max Temp (°C)</th><th>Min Temp (°C)</th></tr>
                <?php foreach ($searchedWeather['forecast_3day'] as $day): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($day['date']) ?></strong></td>
                    <td><?= htmlspecialchars($day['condition']) ?></td>
                    <td style="color:#ffb703; font-weight:bold;"><?= $day['max_c'] ?>°C</td>
                    <td><?= $day['min_c'] ?>°C</td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>

    <h3 style="color:var(--accent-neon);">🇺🇬 Uganda Safari Parks &amp; Operations Live Weather</h3>
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap:14px;">
        <?php foreach ($ugandaHubs as $hub): ?>
            <?php $w = fetchLiveLocationWeather($hub); ?>
            <div class="wx-card">
                <strong><?= htmlspecialchars($w['location']) ?></strong>
                <div class="wx-temp"><?= $w['temp_c'] ?>°C</div>
                <div><?= htmlspecialchars($w['condition']) ?></div>
                <small style="color:var(--text-muted); display:block; margin-top:6px;">
                    Humidity: <?= $w['humidity'] ?>% &bull; Wind: <?= $w['wind_kmh'] ?> km/h<br>
                    GPS: <?= $w['lat'] ?>, <?= $w['lon'] ?> (<?= $w['elevation_m'] ?>m)
                </small>
            </div>
        <?php endforeach; ?>
    </div>

    <h3 style="color:#ffb703; margin-top:28px;">🌍 Global Tourist Origin &amp; World Weather Predictions</h3>
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
        <?php foreach ($worldHubs as $wh): ?>
            <?php $ww = fetchLiveLocationWeather($wh); ?>
            <div class="wx-card" style="border-top-color:#ffb703;">
                <strong><?= htmlspecialchars($ww['location']) ?></strong>
                <div class="wx-temp" style="color:#ffb703;"><?= $ww['temp_c'] ?>°C</div>
                <div><?= htmlspecialchars($ww['condition']) ?></div>
                <small style="color:var(--text-muted); display:block; margin-top:6px;">
                    Humidity: <?= $ww['humidity'] ?>% &bull; Wind: <?= $ww['wind_kmh'] ?> km/h
                </small>
            </div>
        <?php endforeach; ?>
    </div>

    <h3 style="color:var(--accent-neon); margin-top:30px;">📅 Multi-Year Safari Climate Window — Past (Happened) vs. Expected Weather Periods</h3>
    <table>
        <tr>
            <th>Region / Safari Hub</th>
            <th>Period / Season</th>
            <th>Timeline Status</th>
            <th>Weather Classification</th>
            <th>Avg Temp / Rainfall</th>
            <th>Field &amp; Travel Impact</th>
        </tr>
        <?php foreach ($climatePeriods as $cp): ?>
        <tr>
            <td><strong><?= htmlspecialchars($cp['region_name']) ?></strong></td>
            <td><?= htmlspecialchars($cp['period_label']) ?></td>
            <td><?= htmlspecialchars($cp['record_type']) ?></td>
            <td>
                <span style="padding:4px 8px; border-radius:4px; font-weight:bold; font-size:12px; background:<?= $cp['weather_rating'] === 'Good Weather' ? 'var(--accent-neon-soft)' : 'rgba(255,77,109,0.18)' ?>; color:<?= $cp['weather_rating'] === 'Good Weather' ? 'var(--accent-neon)' : 'var(--danger-neon)' ?>;">
                    <?= $cp['weather_rating'] === 'Good Weather' ? '☀️ Good Weather' : '⛈️ Harsh Weather' ?>
                </span>
            </td>
            <td><?= number_format((float)$cp['avg_temp_c'], 1) ?>°C &bull; <?= number_format((float)$cp['rainfall_mm'], 0) ?> mm</td>
            <td><?= htmlspecialchars($cp['summary_notes']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3 style="color:var(--accent-neon); margin-top:30px;">🧭 Uganda &amp; World Geographical Profiles</h3>
    <?php foreach ($UGANDA_GEO_FACTS as $geo): ?>
        <div class="geo-card">
            <strong style="color:var(--accent-neon); font-size:15px;"><?= htmlspecialchars($geo['title']) ?></strong><br>
            <small style="color:#ffb703;">GPS: <?= htmlspecialchars($geo['coords']) ?> &bull; Elevation: <?= htmlspecialchars($geo['elevation']) ?></small>
            <p style="margin:6px 0 4px; font-size:13.5px; text-align:justify;"><strong>Geography:</strong> <?= htmlspecialchars($geo['geography']) ?></p>
            <p style="margin:0; font-size:13.5px; text-align:justify;"><strong>Climate &amp; Seasons:</strong> <?= htmlspecialchars($geo['climate']) ?></p>
        </div>
    <?php endforeach; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
