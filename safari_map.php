<?php
require 'db.php';
require_once 'currency_helper.php';

$feedback = '';

// Handle Custom Route Booking Submission by Tourist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_custom_route'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $packageId    = (int)$_POST['package_id'];
    $travelDate   = $_POST['travel_date'];
    $numGuests    = max(1, (int)$_POST['num_guests']);
    $totalKm      = max(0, (float)$_POST['total_distance_km']);
    $totalHours   = max(0, (float)$_POST['total_travel_hours']);
    $fieldDays    = max(1, (int)$_POST['field_days']);
    $ratePerKmUsd = max(0, (float)$_POST['hire_rate_per_km_usd']);
    $routeSummary = trim($_POST['route_summary']);

    $pkgStmt = $pdo->prepare("SELECT title, price_usd FROM tour_packages WHERE package_id = ?");
    $pkgStmt->execute([$packageId]);
    $pkg = $pkgStmt->fetch();

    if (!$pkg) {
        $feedback = "Error: Please select a valid base safari package.";
    } elseif ($travelDate <= date('Y-m-d')) {
        $feedback = "Error: Departure date must be in the future.";
    } else {
        $packageCostUsd = (float)$pkg['price_usd'] * $numGuests;
        $vehicleHireUsd = $totalKm * $ratePerKmUsd;
        $grandTotalUsd  = $packageCostUsd + $vehicleHireUsd;

        $note = "Custom Mapped Route ({$totalKm} km, {$totalHours} hrs, {$fieldDays} field days @ \${$ratePerKmUsd}/km)";

        $ins = $pdo->prepare("
            INSERT INTO bookings (
                user_id, package_id, travel_date, num_guests, total_cost, status_reason,
                route_summary, total_distance_km, total_travel_hours, field_days, hire_rate_per_km_usd
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ins->execute([
            $_SESSION['user_id'], $packageId, $travelDate, $numGuests, $grandTotalUsd, $note,
            $routeSummary, $totalKm, $totalHours, $fieldDays, $ratePerKmUsd
        ]);

        $newBookingId = (int)$pdo->lastInsertId();
        triggerAdminAlert(
            $pdo,
            'USER_INQUIRY',
            'INFO',
            "🗺️ Custom Map Route Booked (#$newBookingId)",
            "{$_SESSION['full_name']} mapped a {$totalKm} km ({$fieldDays}-day) route for {$pkg['title']}.",
            'Live Safari Map'
        );

        header("Location: payment_checkout.php?booking_id=" . $newBookingId);
        exit;
    }
}

$waypoints = $pdo->query("SELECT * FROM tour_waypoints ORDER BY category ASC, place_name ASC")->fetchAll();
$packages  = $pdo->query("SELECT package_id, title, price_usd, duration_days FROM tour_packages ORDER BY package_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Safari Route &amp; Pin-Drop Map - Pearl Trails Tours &amp; Travel Uganda Ltd</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cloudflare.com" />
    <style>
        #ugandaSafariMap {
            height: 480px;
            width: 100%;
            border-radius: 12px;
            border: 2px solid var(--accent-neon);
            z-index: 1;
            background: #0b1310;
        }
        .map-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(165px, 1fr));
            gap: 12px;
            margin: 16px 0;
        }
        .metric-box {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }
        .metric-box small { color: var(--text-muted); display: block; font-size: 11px; text-transform: uppercase; }
        .metric-box strong { color: var(--accent-neon); font-size: 18px; }
        .leg-list { list-style: none; padding: 0; margin: 10px 0; font-size: 13px; }
        .leg-list li { padding: 8px 10px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
        <div>
            <h2 style="margin:0;">🗺️ Live Safari Route Planner, Pin-Drop Map &amp; Hire Cost Calculator</h2>
            <p style="color:var(--text-muted); margin:4px 0 0;">Click anywhere on the map to drop pins or click saved Hotels, Tour Sites, Camping &amp; Landing Sites to calculate distances, hours, field days &amp; vehicle hire cost.</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" id="btnUndoPin" class="btn btn-edit">↩ Undo Last Pin</button>
            <button type="button" id="btnClearPins" class="btn btn-danger">🗑️ Clear Route</button>
            <a id="googleMapsExternalBtn" href="https://google.com" target="_blank" class="btn">↗ Open in Google Maps</a>
        </div>
    </div>

    <?php if ($feedback): ?>
        <div class="alert <?= strpos($feedback, 'Error') === 0 ? 'error' : '' ?>"><?= htmlspecialchars($feedback) ?></div>
    <?php endif; ?>

    <!-- Saved Waypoints Quick-Add Bar -->
    <div class="card" style="margin-bottom:14px; padding:12px 16px;">
        <strong style="color:var(--accent-neon); font-size:13px;">📍 Quick-Add Official Waypoints (Hotels, Tour Sites, Camping &amp; Landing Sites):</strong>
        <div style="display:flex; flex-wrap:wrap; gap:6px; margin-top:8px;">
            <?php foreach ($waypoints as $wp): ?>
                <button type="button"
                        class="btn btn-edit quick-wp-btn"
                        data-lat="<?= (float)$wp['latitude'] ?>"
                        data-lng="<?= (float)$wp['longitude'] ?>"
                        data-name="<?= htmlspecialchars($wp['place_name'] . ' (' . $wp['category'] . ')') ?>"
                        style="padding:6px 10px; font-size:12px; margin:0;">
                    + <?= htmlspecialchars($wp['place_name']) ?> <small style="color:#ffb703;">[<?= htmlspecialchars($wp['category']) ?>]</small>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Interactive Map Canvas -->
    <div id="ugandaSafariMap"></div>

    <!-- Live Distance, Travel Hours, Field Days & Cost per Km Metrics -->
    <div class="map-metrics">
        <div class="metric-box"><small>Total Route Distance</small><strong id="dispTotalKm">0.0 km</strong></div>
        <div class="metric-box"><small>Est. Driving Hours</small><strong id="dispTotalHours">0.0 hrs</strong></div>
        <div class="metric-box"><small>Total Days in Field</small><strong id="dispFieldDays">1 Day(s)</strong></div>
        <div class="metric-box"><small>Hire Rate / Km (<?= htmlspecialchars($activeCurrency) ?>)</small><strong id="dispRatePerKm"><?= formatMoney(5.0) ?>/km</strong></div>
        <div class="metric-box"><small>Total Vehicle Hire Cost</small><strong id="dispHireCost" style="color:#ffb703;"><?= formatMoney(0) ?></strong></div>
    </div>

    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap:20px;">
        <!-- Point-to-Point Route Breakdown -->
        <div class="card">
            <h3 style="margin-top:0; color:var(--accent-neon);">📌 Point-to-Point Route Legs &amp; Distances</h3>
            <ul class="leg-list" id="routeLegsList">
                <li style="color:var(--text-muted);">Click on the map or select a waypoint above to start mapping your route.</li>
            </ul>
        </div>

        <!-- Book Custom Mapped Tour -->
        <div class="card">
            <h3 style="margin-top:0; color:var(--accent-neon);">🚙 Book This Mapped Route &amp; Vehicle Hire</h3>
            <form method="POST">
                <input type="hidden" name="total_distance_km" id="inputTotalKm" value="0">
                <input type="hidden" name="total_travel_hours" id="inputTotalHours" value="0">
                <input type="hidden" name="route_summary" id="inputRouteSummary" value="">

                <label>Select Base Safari Package *</label>
                <select name="package_id" required>
                    <?php foreach ($packages as $pkg): ?>
                        <option value="<?= (int)$pkg['package_id'] ?>">
                            <?= htmlspecialchars($pkg['title']) ?> (Base: <?= formatMoney((float)$pkg['price_usd']) ?>/guest)
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="grid" style="grid-template-columns: 1fr 1fr; gap:10px; margin-top:0;">
                    <div>
                        <label>Departure Date *</label>
                        <input type="date" name="travel_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                    </div>
                    <div>
                        <label>Number of Tourists *</label>
                        <input type="number" name="num_guests" min="1" max="20" value="2" required>
                    </div>
                    <div>
                        <label>Total Days in Field *</label>
                        <input type="number" name="field_days" id="inputFieldDays" min="1" max="60" value="1" required>
                    </div>
                    <div>
                        <label>Vehicle Hire Rate (USD/km) *</label>
                        <select name="hire_rate_per_km_usd" id="inputRatePerKm">
                            <option value="1.20">Standard Safari Van ($3.50/km)</option>
                            <option value="1.50" selected>4x4 Land Cruiser Pop-Up ($4.50/km)</option>
                            <option value="2.00">Extended VIP Land Cruiser ($6.00/km)</option>
                        </select>
                    </div>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <button type="submit" name="book_custom_route" style="width:100%; margin-top:10px;">Confirm Mapped Route &amp; Proceed to Pay</button>
                <?php else: ?>
                    <a href="login.php" class="btn" style="width:100%; box-sizing:border-box; margin-top:10px;">Login to Book Mapped Route</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var fxRate = <?= (float)$currentRate ?>;
    var fxSymbol = <?= json_encode($currencySymbol) ?>;
    var fxCurrency = <?= json_encode($activeCurrency) ?>;
    var decimals = ['UGX', 'TZS', 'RWF'].includes(fxCurrency) ? 0 : 2;

    function fmtLocal(usdVal) {
        var val = usdVal * fxRate;
        return fxSymbol + val.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
    }

    var routePins = [];
    var routeMarkers = [];
    var currentTotalKm = 0;
    var currentTotalHours = 0;
    var map = null;
    var routePolyline = null;
    var savedWaypoints = <?= json_encode($waypoints) ?>;

    // Initialize Interactive Leaflet Map (with automatic fallback if offline)
    if (typeof L !== 'undefined') {
        map = L.map('ugandaSafariMap').setView([1.3733, 32.2903], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap | Pearl Trails Tours & Travel Uganda Ltd'
        }).addTo(map);

        // Force Leaflet to recalculate container dimensions cleanly
        setTimeout(function() { map.invalidateSize(); }, 250);

        routePolyline = L.polyline([], { color: '#00f59b', weight: 4 }).addTo(map);

        savedWaypoints.forEach(function(wp) {
            var lat = parseFloat(wp.latitude);
            var lng = parseFloat(wp.longitude);
            var marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(
                '<strong>' + wp.place_name + '</strong><br>' +
                '<span style="color:#00a86b;">Category: ' + wp.category + '</span><br>' +
                '<small>' + (wp.notes || '') + '</small>'
            );
            marker.on('click', function() {
                addRoutePin(lat, lng, wp.place_name + ' (' + wp.category + ')');
            });
        });

        map.on('click', function(e) {
            var lat = parseFloat(e.latlng.lat.toFixed(5));
            var lng = parseFloat(e.latlng.lng.toFixed(5));
            var label = prompt('Enter a name for this dropped pin (e.g., Hotel Stop, Game Track, Camping Point):', 'Custom Pin #' + (routePins.length + 1) + ' (' + lat + ', ' + lng + ')');
            if (label !== null && label.trim() !== '') {
                addRoutePin(lat, lng, label.trim());
            }
        });
    } else {
        // Offline / Localhost Interactive Uganda Route Canvas Fallback
        var mapBox = document.getElementById('ugandaSafariMap');
        mapBox.innerHTML = '<canvas id="offlineUgandaCanvas" width="900" height="460" style="width:100%; height:100%; cursor:crosshair; display:block;"></canvas>';
        var canvas = document.getElementById('offlineUgandaCanvas');
        canvas.addEventListener('click', function(e) {
            var rect = canvas.getBoundingClientRect();
            var xRatio = (e.clientX - rect.left) / rect.width;
            var yRatio = (e.clientY - rect.top) / rect.height;
            // Map canvas click to Uganda GPS bounds (Lat: -1.5 to 4.2, Lng: 29.5 to 35.0)
            var lat = parseFloat((4.2 - yRatio * 5.7).toFixed(5));
            var lng = parseFloat((29.5 + xRatio * 5.5).toFixed(5));
            var label = prompt('Enter a name for this dropped pin:', 'Custom Pin #' + (routePins.length + 1) + ' (' + lat + ', ' + lng + ')');
            if (label !== null && label.trim() !== '') {
                addRoutePin(lat, lng, label.trim());
            }
        });
    }

    function drawOfflineCanvas() {
        var canvas = document.getElementById('offlineUgandaCanvas');
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#0b1310';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Draw Coordinate Grid & Uganda Reference Header
        ctx.strokeStyle = '#1f362d';
        ctx.lineWidth = 1;
        for (var gx = 0; gx < canvas.width; gx += 75) { ctx.beginPath(); ctx.moveTo(gx, 0); ctx.lineTo(gx, canvas.height); ctx.stroke(); }
        for (var gy = 0; gy < canvas.height; gy += 75) { ctx.beginPath(); ctx.moveTo(0, gy); ctx.lineTo(canvas.width, gy); ctx.stroke(); }

        ctx.fillStyle = '#00f59b';
        ctx.font = 'bold 14px Arial';
        ctx.fillText('PEARL TRAILS UGANDA — INTERACTIVE GPS ROUTE PLOTTER (Click Anywhere to Drop Pin)', 18, 26);

        function toXY(lat, lng) {
            var x = ((lng - 29.5) / 5.5) * canvas.width;
            var y = ((4.2 - lat) / 5.7) * canvas.height;
            return { x: x, y: y };
        }

        // Draw Saved Waypoints
        savedWaypoints.forEach(function(wp) {
            var pt = toXY(parseFloat(wp.latitude), parseFloat(wp.longitude));
            ctx.fillStyle = '#38bdf8';
            ctx.beginPath(); ctx.arc(pt.x, pt.y, 5, 0, Math.PI * 2); ctx.fill();
            ctx.fillStyle = '#9eb3aa';
            ctx.font = '11px Arial';
            ctx.fillText(wp.place_name, pt.x + 8, pt.y + 4);
        });

        // Draw Active Route Lines & Pins
        if (routePins.length > 0) {
            ctx.strokeStyle = '#00f59b';
            ctx.lineWidth = 3;
            ctx.beginPath();
            routePins.forEach(function(p, idx) {
                var pt = toXY(p.lat, p.lng);
                if (idx === 0) ctx.moveTo(pt.x, pt.y);
                else ctx.lineTo(pt.x, pt.y);
            });
            ctx.stroke();

            routePins.forEach(function(p, idx) {
                var pt = toXY(p.lat, p.lng);
                ctx.fillStyle = '#ffb703';
                ctx.beginPath(); ctx.arc(pt.x, pt.y, 8, 0, Math.PI * 2); ctx.fill();
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 12px Arial';
                ctx.fillText((idx + 1) + '. ' + p.name, pt.x + 11, pt.y - 6);
            });
        }
    }

    function calcRoadKm(lat1, lon1, lat2, lon2) {
        var R = 6371;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return (R * c) * 1.28;
    }

    function addRoutePin(lat, lng, name) {
        routePins.push({ lat: lat, lng: lng, name: name });
        if (map && typeof L !== 'undefined') {
            var m = L.circleMarker([lat, lng], {
                radius: 8,
                fillColor: '#ffb703',
                color: '#00f59b',
                weight: 2,
                fillOpacity: 0.95
            }).addTo(map).bindTooltip(routePins.length + '. ' + name, { permanent: true, direction: 'top' });
            routeMarkers.push(m);
            map.panTo([lat, lng]);
        } else {
            drawOfflineCanvas();
        }
        updateRouteCalculations();
    }

    function updateRouteCalculations() {
        if (routePolyline) {
            routePolyline.setLatLngs(routePins.map(function(p) { return [p.lat, p.lng]; }));
        }

        var legsUl = document.getElementById('routeLegsList');
        legsUl.innerHTML = '';
        currentTotalKm = 0;

        if (routePins.length === 0) {
            legsUl.innerHTML = '<li style="color:var(--text-muted);">Click on the map or select a waypoint above to start mapping your route.</li>';
        } else if (routePins.length === 1) {
            // Fixed: Index  for first pin
            legsUl.innerHTML = '<li><span><strong>Start Point:</strong> ' + routePins.name + '</span><strong style="color:var(--accent-neon);">0.0 km</strong></li>';
        } else {
            for (var i = 0; i < routePins.length - 1; i++) {
                var p1 = routePins[i];
                var p2 = routePins[i + 1];
                var legKm = calcRoadKm(p1.lat, p1.lng, p2.lat, p2.lng);
                var legHrs = legKm / 55;
                currentTotalKm += legKm;

                var li = document.createElement('li');
                li.innerHTML = '<span><strong>Leg ' + (i + 1) + ':</strong> ' + p1.name + ' &rarr; ' + p2.name + '</span>' +
                               '<strong style="color:var(--accent-neon);">' + legKm.toFixed(1) + ' km (~' + legHrs.toFixed(1) + ' hrs)</strong>';
                legsUl.appendChild(li);
            }
        }

        currentTotalHours = currentTotalKm / 55;
        var suggestedDays = Math.max(1, Math.ceil(currentTotalHours / 6));
        document.getElementById('inputFieldDays').value = suggestedDays;

        if (routePins.length >= 2) {
            // Fixed: Index  for origin pin
            var origin = routePins.lat + ',' + routePins.lng;
            var dest = routePins[routePins.length - 1].lat + ',' + routePins[routePins.length - 1].lng;
            document.getElementById('googleMapsExternalBtn').href = 'https://google.com' + origin + '&destination=' + dest + '&travelmode=driving';
        }

        recalcMapCosts();
    }

    function recalcMapCosts() {
        var rateUsd = parseFloat(document.getElementById('inputRatePerKm').value) || 1.50;
        var fieldDays = parseInt(document.getElementById('inputFieldDays').value, 10) || 1;
        var totalHireUsd = currentTotalKm * rateUsd;

        document.getElementById('dispTotalKm').textContent = currentTotalKm.toFixed(1) + ' km';
        document.getElementById('dispTotalHours').textContent = currentTotalHours.toFixed(1) + ' hrs';
        document.getElementById('dispFieldDays').textContent = fieldDays + ' Day(s)';
        document.getElementById('dispRatePerKm').textContent = fmtLocal(rateUsd) + '/km';
        document.getElementById('dispHireCost').textContent = fmtLocal(totalHireUsd);
        document.getElementById('inputTotalKm').value = currentTotalKm.toFixed(2);
        document.getElementById('inputTotalHours').value = currentTotalHours.toFixed(2);
        document.getElementById('inputRouteSummary').value = routePins.map(function(p) { return p.name; }).join(' -> ');
    }

    document.querySelectorAll('.quick-wp-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var lat = parseFloat(this.getAttribute('data-lat'));
            var lng = parseFloat(this.getAttribute('data-lng'));
            var name = this.getAttribute('data-name');
            addRoutePin(lat, lng, name);
        });
    });

    document.getElementById('btnUndoPin').addEventListener('click', function() {
        if (routePins.length === 0) return;
        routePins.pop();
        var lastMarker = routeMarkers.pop();
        if (lastMarker && map) map.removeLayer(lastMarker);
        drawOfflineCanvas();
        updateRouteCalculations();
    });

    document.getElementById('btnClearPins').addEventListener('click', function() {
        routePins = [];
        routeMarkers.forEach(function(m) { if (map) map.removeLayer(m); });
        routeMarkers = [];
        drawOfflineCanvas();
        updateRouteCalculations();
    });

    document.getElementById('inputFieldDays').addEventListener('input', recalcMapCosts);
    document.getElementById('inputRatePerKm').addEventListener('change', recalcMapCosts);

    if (!map) drawOfflineCanvas();
});
</script>
<?php include 'footer.php'; ?>
</body>
</html>
