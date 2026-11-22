<?php
ob_start();
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-KEY');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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

// Official System API Key (Change this to a strong secret in production)
define('PEARL_TRAILS_API_KEY', 'PT_UG_2026_LIVE_API_98421');

function apiResponse(int $statusCode, bool $success, string $message, $data = null): void {
    http_response_code($statusCode);
    echo json_encode([
        'status' => $statusCode,
        'success' => $success,
        'company' => 'Pearl Trails Tours & Travel Uganda Ltd',
        'timestamp' => date('c'),
        'message' => $message,
        'data' => $data
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Verify API Key for Protected Endpoints
$providedKey = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? '';
$resource = strtolower(trim($_GET['resource'] ?? 'status'));

if ($resource !== 'status' && $providedKey !== PEARL_TRAILS_API_KEY) {
    apiResponse(401, false, 'Unauthorized: Invalid or missing X-API-KEY.');
}

// =========================================================================
// GET ENDPOINTS (Read Packages, Waypoints, Weather, Holidays, Verify Ticket)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($resource) {
        case 'status':
            apiResponse(200, true, 'Pearl Trails REST API v1.0 is online and operational.', [
                'endpoints' => [
                    'GET /api.php?resource=packages&api_key=YOUR_KEY',
                    'GET /api.php?resource=waypoints&api_key=YOUR_KEY',
                    'GET /api.php?resource=weather&place=Bwindi&api_key=YOUR_KEY',
                    'GET /api.php?resource=holidays&api_key=YOUR_KEY',
                    'GET /api.php?resource=verify_ticket&booking_id=1&api_key=YOUR_KEY',
                    'POST /api.php?resource=create_booking&api_key=YOUR_KEY'
                ]
            ]);

        case 'packages':
            $rows = $pdo->query("SELECT package_id, title, destination, duration_days, max_capacity, price_usd, discount_percent, description FROM tour_packages ORDER BY package_id DESC")->fetchAll(PDO::FETCH_ASSOC);
            apiResponse(200, true, 'Safari packages retrieved successfully.', $rows);

        case 'waypoints':
            $rows = $pdo->query("SELECT waypoint_id, place_name, category, district_or_park, latitude, longitude, notes FROM tour_waypoints ORDER BY category ASC")->fetchAll(PDO::FETCH_ASSOC);
            apiResponse(200, true, 'Mapped hotels, tour sites, camping & landing sites retrieved.', $rows);

        case 'weather':
            $place = trim($_GET['place'] ?? 'Kampala');
            if (function_exists('fetchLiveLocationWeather')) {
                $wx = fetchLiveLocationWeather($place);
                apiResponse(200, true, "Live meteorological data for {$wx['location']}.", $wx);
            }
            apiResponse(503, false, 'Weather engine unavailable.');

        case 'holidays':
            $rows = $pdo->query("SELECT holiday_date, holiday_name FROM uganda_holidays WHERE is_active = 1 ORDER BY holiday_date ASC")->fetchAll(PDO::FETCH_ASSOC);
            apiResponse(200, true, 'Qualifying Ugandan public holidays retrieved.', $rows);

        case 'verify_ticket':
            $bid = (int)($_GET['booking_id'] ?? 0);
            $stmt = $pdo->prepare("
                SELECT b.booking_id, u.full_name AS tourist_name, t.title AS package_title,
                       b.travel_date, b.num_guests, b.total_cost, b.status
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN tour_packages t ON b.package_id = t.package_id
                WHERE b.booking_id = ?
            ");
            $stmt->execute([$bid]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$ticket) {
                apiResponse(404, false, "Booking #$bid not found.");
            }
            apiResponse(200, true, "Boarding voucher #$bid verified.", $ticket);

        default:
            apiResponse(400, false, 'Unknown GET resource requested.');
    }
}
// =========================================================================
// POST ENDPOINTS (External Partner Booking Creation)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    if ($resource === 'create_booking') {
        $userId     = (int)($payload['user_id'] ?? 0);
        $packageId  = (int)($payload['package_id'] ?? 0);
        $travelDate = trim($payload['travel_date'] ?? '');
        $numGuests  = max(1, (int)($payload['num_guests'] ?? 1));

        if ($userId <= 0 || $packageId <= 0 || $travelDate === '') {
            apiResponse(422, false, 'Missing required fields: user_id, package_id, and travel_date.');
        }

        $pkgStmt = $pdo->prepare("SELECT title, price_usd FROM tour_packages WHERE package_id = ?");
        $pkgStmt->execute([$packageId]);
        $pkg = $pkgStmt->fetch(PDO::FETCH_ASSOC);
        if (!$pkg) {
            apiResponse(404, false, 'Selected tour package does not exist.');
        }

        $totalUsd = (float)$pkg['price_usd'] * $numGuests;
        $ins = $pdo->prepare("INSERT INTO bookings (user_id, package_id, travel_date, num_guests, total_cost, status_reason) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$userId, $packageId, $travelDate, $numGuests, $totalUsd, 'Booked via External REST API']);
        $newId = (int)$pdo->lastInsertId();

        apiResponse(201, true, 'Safari booking created successfully via API.', [
            'booking_id' => $newId,
            'package' => $pkg['title'],
            'travel_date' => $travelDate,
            'num_guests' => $numGuests,
            'total_cost_usd' => $totalUsd,
            'status' => 'Pending'
        ]);
    }

    apiResponse(400, false, 'Unknown POST resource requested.');
}

apiResponse(405, false, 'HTTP Method Not Allowed.');
