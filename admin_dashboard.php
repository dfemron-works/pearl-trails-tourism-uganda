<?php
require 'db.php';
require_once 'currency_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';

// 1. CREATE Tour Package (with Optional Weekend & Ugandan Holiday Offer %)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_package'])) {
    $discount = max(0, min(90, (int)($_POST['discount_percent'] ?? 0)));
    $promoValidity = ($discount > 0) ? 'WEEKENDS_AND_UG_HOLIDAYS' : 'NONE';

    $stmt = $pdo->prepare("
        INSERT INTO tour_packages (title, destination, duration_days, price_usd, discount_percent, promo_validity, max_capacity, description) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        trim($_POST['title']),
        trim($_POST['destination']),
        (int)$_POST['duration_days'],
        (float)$_POST['price_usd'],
        $discount,
        $promoValidity,
        (int)$_POST['max_capacity'],
        trim($_POST['description'])
    ]);
    $message = $discount > 0
        ? "Tour package created with a {$discount}% Weekend & Ugandan Holiday Offer!"
        : "Tour package created successfully!";
}

// 2. DELETE Tour Package
if (isset($_GET['delete_pkg'])) {
    $stmt = $pdo->prepare("DELETE FROM tour_packages WHERE package_id = ?");
    $stmt->execute([(int)$_GET['delete_pkg']]);
    $message = "Tour package deleted.";
}

// 3. ADD or DELETE Ugandan Public Holiday
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_holiday'])) {
    $hDate = $_POST['holiday_date'];
    $hName = trim($_POST['holiday_name']);
    if ($hDate !== '' && $hName !== '') {
        try {
            $stmt = $pdo->prepare("INSERT INTO uganda_holidays (holiday_date, holiday_name, is_active) VALUES (?, ?, 1)");
            $stmt->execute([$hDate, $hName]);
            $message = "Ugandan Public Holiday '{$hName}' ({$hDate}) added to promotional calendar.";
        } catch (PDOException $e) {
            $message = "Error: That date is already listed in the Ugandan holidays table.";
        }
    }
}

if (isset($_GET['delete_holiday'])) {
    $stmt = $pdo->prepare("DELETE FROM uganda_holidays WHERE holiday_id = ?");
    $stmt->execute([(int)$_GET['delete_holiday']]);
    $message = "Public holiday removed.";
}

// 4. UPDATE Booking Status + Reason Note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_booking_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $status = $_POST['status'];
    $reason = trim($_POST['status_reason']);
    if ($reason === '') {
        $reason = "Status updated to " . $status . " by Administrator";
    }
    $stmt = $pdo->prepare("UPDATE bookings SET status = ?, status_reason = ? WHERE booking_id = ?");
    $stmt->execute([$status, $reason, $booking_id]);
    $message = "Booking #" . $booking_id . " updated to " . $status . ".";
}

// 5. UPLOAD Gallery Photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_photo'])) {
    $title = trim($_POST['photo_title']);
    $location = trim($_POST['location']);
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($ext, $allowed, true) && $_FILES['photo']['size'] <= 3000000) {
        $target = 'uploads/' . uniqid('safari_') . '.' . $ext;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $stmt = $pdo->prepare("INSERT INTO gallery (title, location, image_url) VALUES (?, ?, ?)");
            $stmt->execute([$title, $location, $target]);
            $message = "Safari photo uploaded to gallery!";
        }
    } else {
        $message = "Error: Invalid image format or file exceeds 3MB.";
    }
}

// 6. VERIFY OR REJECT Electronic Payment Receipt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_payment'])) {
    $paymentId = (int)$_POST['payment_id'];
    $bookingId = (int)$_POST['booking_id'];
    $decision  = $_POST['payment_decision'];

    $updPay = $pdo->prepare("UPDATE payments SET payment_status = ? WHERE payment_id = ?");
    $updPay->execute([$decision, $paymentId]);

    if ($decision === 'Verified & Paid') {
        $updBook = $pdo->prepare("UPDATE bookings SET status = 'Confirmed', status_reason = ? WHERE booking_id = ?");
        $updBook->execute(["Electronic Payment #$paymentId Verified & Confirmed by Admin", $bookingId]);
        $message = "Payment #$paymentId verified! Booking #$bookingId is now Confirmed.";
    } else {
        $updBook = $pdo->prepare("UPDATE bookings SET status = 'Pending', status_reason = ? WHERE booking_id = ?");
        $updBook->execute(["Payment #$paymentId Rejected — Please submit valid transaction reference", $bookingId]);
        $message = "Payment #$paymentId marked as Rejected.";
    }
}
// 7. ADD OR DELETE Map Waypoint (Hotel, Tour Site, Camping Site, Landing Site)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_waypoint'])) {
    $wpStmt = $pdo->prepare("
        INSERT INTO tour_waypoints (place_name, category, district_or_park, latitude, longitude, notes) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $wpStmt->execute([
        trim($_POST['place_name']),
        $_POST['category'],
        trim($_POST['district_or_park']),
        (float)$_POST['latitude'],
        (float)$_POST['longitude'],
        trim($_POST['notes'])
    ]);
    $message = "New map location '" . htmlspecialchars($_POST['place_name']) . "' published to live client maps!";
}

if (isset($_GET['delete_wp'])) {
    $pdo->prepare("DELETE FROM tour_waypoints WHERE waypoint_id = ?")->execute([(int)$_GET['delete_wp']]);
    $message = "Map location removed.";
}

$allWaypoints = $pdo->query("SELECT * FROM tour_waypoints ORDER BY waypoint_id DESC")->fetchAll();

// Fetch All Records Cleanly
$packages = $pdo->query("SELECT * FROM tour_packages ORDER BY package_id DESC")->fetchAll();
$holidays = $pdo->query("SELECT * FROM uganda_holidays ORDER BY holiday_date ASC")->fetchAll();

// Explicit Customer Bookings Query (Guarantees exact booking_id per tourist row)
$allBookings = $pdo->query("
    SELECT b.booking_id, b.user_id, b.package_id, b.travel_date, b.num_guests, b.total_cost, b.status, b.status_reason,
           u.full_name, u.email, t.title AS package_title
    FROM bookings b
    INNER JOIN users u ON b.user_id = u.user_id
    INNER JOIN tour_packages t ON b.package_id = t.package_id
    ORDER BY b.booking_id DESC
")->fetchAll();

$allPayments = $pdo->query("
    SELECT p.*, u.full_name, t.title AS package_title
    FROM payments p
    JOIN users u ON p.user_id = u.user_id
    JOIN bookings b ON p.booking_id = b.booking_id
    JOIN tour_packages t ON b.package_id = t.package_id
    ORDER BY p.paid_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Records Manager - Pearl Trails Tours &amp; Travel Ug Ltd</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-Confirmed { background: var(--accent-neon-soft); color: var(--accent-neon); }
        .badge-Concluded { background: rgba(56, 189, 248, 0.15); color: #38bdf8; }
        .badge-Pending { background: rgba(255, 183, 3, 0.15); color: var(--warn-neon); }
        .badge-Cancelled { background: rgba(255, 77, 109, 0.15); color: var(--danger-neon); }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Records Manager — Safari Packages, Bookings &amp; Electronic Payments</h2>
    <?php if ($message): ?>
        <div class="alert <?= strpos($message, 'Error') === 0 ? 'error' : '' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- SECTION 1: ADD TOUR PACKAGE -->
    <div class="card">
        <h3>1. Add New Tour Package &amp; Promotional Offer</h3>
        <form method="POST">
            <input type="text" name="title" placeholder="Safari Package Title (e.g., Bwindi Gorilla Trekking)" required>
            <input type="text" name="destination" placeholder="Destination (e.g., Bwindi Impenetrable Forest)" required>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-top: 0;">
                <div><label>Duration (Days)</label><input type="number" name="duration_days" min="1" required></div>
                <div><label>Base Price (USD $)</label><input type="number" step="0.01" name="price_usd" min="1" required></div>
                <div><label>Offer Discount % (Weekends &amp; UG Holidays)</label><input type="number" name="discount_percent" min="0" max="90" value="0"></div>
                <div><label>Max Group Capacity</label><input type="number" name="max_capacity" min="1" value="8" required></div>
            </div>
            <textarea name="description" placeholder="Detailed safari itinerary and inclusions..." required></textarea>
            <button type="submit" name="add_package">Create Package</button>
        </form>
    </div>

    <!-- SECTION 2: MANAGE TOUR PACKAGES -->
    <h2>2. Existing Safari Packages &amp; Active Offers (<?= htmlspecialchars($activeCurrency) ?>)</h2>
    <table>
        <tr>
            <th>Title</th>
            <th>Destination</th>
            <th>Base Price</th>
            <th>Offer Rate (Weekends &amp; UG Holidays)</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($packages as $p): ?>
        <?php $disc = (int)($p['discount_percent'] ?? 0); ?>
        <tr>
            <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
            <td><?= htmlspecialchars($p['destination']) ?></td>
            <td><?= formatMoney((float)$p['price_usd']) ?></td>
            <td>
                <?php if ($disc > 0): ?>
                    <span class="badge badge-Confirmed"><?= $disc ?>% OFF</span><br>
                    <small style="color:var(--accent-neon);">Promo: <?= formatMoney((float)$p['price_usd'] * (1 - $disc / 100)) ?></small>
                <?php else: ?>
                    <span style="color:var(--text-muted);">Standard Price</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="edit_package.php?id=<?= (int)$p['package_id'] ?>" class="btn btn-edit">Edit</a>
                <a href="?delete_pkg=<?= (int)$p['package_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this package?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- SECTION 3: MANAGE UGANDAN PUBLIC HOLIDAYS -->
    <h2>3. Official Ugandan Public Holidays Calendar</h2>
    <div class="card">
        <form method="POST" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <input type="date" name="holiday_date" required style="width:auto; margin:0;">
            <input type="text" name="holiday_name" placeholder="Holiday Name (e.g., Independence Day)" required style="flex:1; min-width:220px; margin:0;">
            <button type="submit" name="add_holiday">Add Public Holiday</button>
        </form>
    </div>
    <table>
        <tr><th>Date</th><th>Public Holiday Name</th><th>Day of Week</th><th>Action</th></tr>
        <?php foreach ($holidays as $h): ?>
        <tr>
            <td><strong><?= htmlspecialchars($h['holiday_date']) ?></strong></td>
            <td><?= htmlspecialchars($h['holiday_name']) ?></td>
            <td><?= date('l', strtotime($h['holiday_date'])) ?></td>
            <td><a href="?delete_holiday=<?= (int)$h['holiday_id'] ?>" class="btn btn-danger">Remove</a></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- SECTION 4: MANAGE CUSTOMER BOOKINGS & INDIVIDUAL E-TICKETS -->
    <h2>4. Manage Customer Bookings &amp; Issue E-Tickets</h2>
    <table>
        <tr>
            <th>Booking ID</th>
            <th>Tourist</th>
            <th>Package</th>
            <th>Travel Date</th>
            <th>Total (<?= htmlspecialchars($activeCurrency) ?>)</th>
            <th>Current Status &amp; Note</th>
            <th>Update Status &amp; View Voucher</th>
        </tr>
        <?php foreach ($allBookings as $b): ?>
        <tr>
            <td><strong>#<?= (int)$b['booking_id'] ?></strong></td>
            <td>
                <strong><?= htmlspecialchars($b['full_name']) ?></strong><br>
                <small style="color:var(--text-muted);"><?= htmlspecialchars($b['email']) ?></small>
            </td>
            <td><?= htmlspecialchars($b['package_title']) ?> (<?= (int)$b['num_guests'] ?> guests)</td>
            <td><?= htmlspecialchars($b['travel_date']) ?></td>
            <td><?= formatMoney((float)$b['total_cost']) ?></td>
            <td>
                <span class="badge badge-<?= htmlspecialchars($b['status']) ?>"><?= htmlspecialchars($b['status']) ?></span><br>
                <small style="color:var(--text-muted);"><?= htmlspecialchars($b['status_reason'] ?? '') ?></small>
            </td>
            <td>
                <form method="POST" style="display:flex; flex-direction:column; gap:5px; margin:0 0 6px 0;">
                    <input type="hidden" name="booking_id" value="<?= (int)$b['booking_id'] ?>">
                    <select name="status" style="margin:0;">
                        <option value="Pending" <?= $b['status']=='Pending'?'selected':'' ?>>Pending</option>
                        <option value="Confirmed" <?= $b['status']=='Confirmed'?'selected':'' ?>>Confirmed (Successful)</option>
                        <option value="Concluded" <?= $b['status']=='Concluded'?'selected':'' ?>>Concluded</option>
                        <option value="Cancelled" <?= $b['status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
                    </select>
                    <input type="text" name="status_reason" placeholder="Reason (e.g. Awaiting deposit)" value="<?= htmlspecialchars($b['status_reason'] ?? '') ?>" style="margin:0;">
                    <button type="submit" name="update_booking_status">Update</button>
                </form>

                <a href="voucher.php?booking_id=<?= (int)$b['booking_id'] ?>" class="btn btn-edit" style="display:block; text-align:center; padding:6px 8px; font-size:12px;">
                    🎟️ View E-Ticket (#<?= (int)$b['booking_id'] ?>)
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- SECTION 5: VERIFY ELECTRONIC FUNDS TRANSFERS & MOBILE MONEY -->
    <h2>5. Verify Electronic Funds Transfers &amp; Mobile Money Receipts</h2>
    <table>
        <tr>
            <th>Pay ID</th>
            <th>Booking &amp; Tourist</th>
            <th>Payment Method</th>
            <th>Company Destination A/C</th>
            <th>Sender &amp; Transaction Ref</th>
            <th>Amount Paid</th>
            <th>Verification Action</th>
        </tr>
        <?php if (empty($allPayments)): ?>
            <tr><td colspan="7" style="text-align:center;">No electronic payment receipts submitted yet.</td></tr>
        <?php else: ?>
            <?php foreach ($allPayments as $pay): ?>
            <tr>
                <td>#<?= (int)$pay['payment_id'] ?></td>
                <td>
                    <strong>Booking #<?= (int)$pay['booking_id'] ?></strong><br>
                    <span><?= htmlspecialchars($pay['full_name']) ?></span>
                </td>
                <td><strong style="color:var(--accent-neon);"><?= htmlspecialchars($pay['payment_method']) ?></strong></td>
                <td><small><?= htmlspecialchars($pay['company_destination_account']) ?></small></td>
                <td>
                    <span>From: <?= htmlspecialchars($pay['payer_account_or_phone']) ?></span><br>
                    <strong style="color:#ffb703;">Ref: <?= htmlspecialchars($pay['transaction_reference']) ?></strong>
                </td>
                <td><strong><?= htmlspecialchars($pay['amount_in_currency']) ?></strong></td>
                <td>
                    <form method="POST" style="display:flex; flex-direction:column; gap:5px; margin:0;">
                        <input type="hidden" name="payment_id" value="<?= (int)$pay['payment_id'] ?>">
                        <input type="hidden" name="booking_id" value="<?= (int)$pay['booking_id'] ?>">
                        <select name="payment_decision" style="margin:0; padding:6px;">
                            <option value="Pending Verification" <?= $pay['payment_status'] === 'Pending Verification' ? 'selected' : '' ?>>Pending Verification</option>
                            <option value="Verified & Paid" <?= $pay['payment_status'] === 'Verified & Paid' ? 'selected' : '' ?>>✅ Verified &amp; Paid</option>
                            <option value="Rejected" <?= $pay['payment_status'] === 'Rejected' ? 'selected' : '' ?>>❌ Rejected</option>
                        </select>
                        <button type="submit" name="verify_payment" style="padding:6px 10px;">Save Status</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <div class="card" style="margin-top:26px;">
        <h3>6. Upload Safari Gallery Photo</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="photo_title" placeholder="Photo Caption" required>
            <input type="text" name="location" placeholder="Location (e.g., Murchison Falls)" required>
            <input type="file" name="photo" accept="image/*" required>
            <button type="submit" name="upload_photo">Upload Photo</button>
        </form>
            <!-- SECTION 7: LIVE SAFARI MAP & WAYPOINT MANAGER -->
    <h2 style="margin-top:32px;">7. Live Map Manager — Pin Hotels, Tour Sites, Camping &amp; Landing Sites</h2>
    <div class="card">
        <p style="color:var(--text-muted); margin-top:0;">
            Click anywhere on the map below to capture exact GPS coordinates automatically, then label the Hotel, Tour Site, Camping Site, or Landing Site for client navigation.
        </p>

                <link rel="stylesheet" href="https://unpkg.com" />
        <div id="adminLiveMap" style="height:360px; width:100%; border-radius:10px; border:2px solid var(--accent-neon); margin-bottom:14px; background:#0b1310; z-index:1;"></div>

        <form method="POST" class="grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px; margin-top:0;">
            <input type="text" name="place_name" placeholder="Place Name (e.g., Paraa Lodge)" required style="margin:0;">
            <select name="category" style="margin:0;">
                <option value="Tour Site">Tour Site</option>
                <option value="Hotel / Lodge">Hotel / Lodge</option>
                <option value="Camping Site">Camping Site</option>
                <option value="Landing Site">Landing Site</option>
            </select>
            <input type="text" name="district_or_park" placeholder="District / National Park" required style="margin:0;">
            <input type="number" step="any" name="latitude" id="adminLatInput" placeholder="Latitude (Click Map)" required style="margin:0;">
            <input type="number" step="any" name="longitude" id="adminLngInput" placeholder="Longitude (Click Map)" required style="margin:0;">
            <input type="text" name="notes" placeholder="Directions / Notes for Clients" style="margin:0;">
            <button type="submit" name="add_waypoint">Publish Pin to Live Map</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Place Name</th>
            <th>Category</th>
            <th>District / Park</th>
            <th>GPS Coordinates</th>
            <th>Directions Note</th>
            <th>Action</th>
        </tr>
        <?php foreach ($allWaypoints as $wp): ?>
        <tr>
            <td><strong><?= htmlspecialchars($wp['place_name']) ?></strong></td>
            <td><span class="badge badge-Confirmed"><?= htmlspecialchars($wp['category']) ?></span></td>
            <td><?= htmlspecialchars($wp['district_or_park']) ?></td>
            <td><code><?= number_format((float)$wp['latitude'], 4) ?>, <?= number_format((float)$wp['longitude'], 4) ?></code></td>
            <td><?= htmlspecialchars($wp['notes']) ?></td>
            <td>
                <a href="?delete_wp=<?= (int)$wp['waypoint_id'] ?>" class="btn btn-danger" onclick="return confirm('Remove this map pin?');">Remove</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <script src="https://unpkg.com"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var existingAdminPins = <?= json_encode($allWaypoints) ?>;
        var latInput = document.getElementById('adminLatInput');
        var lngInput = document.getElementById('adminLngInput');
        var adminMapBox = document.getElementById('adminLiveMap');

        if (typeof L !== 'undefined') {
            var adminMap = L.map('adminLiveMap').setView([1.3733, 32.2903], 7);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; OpenStreetMap | Pearl Trails Admin Map'
            }).addTo(adminMap);

            setTimeout(function() { adminMap.invalidateSize(); }, 250);

            existingAdminPins.forEach(function(wp) {
                L.marker([parseFloat(wp.latitude), parseFloat(wp.longitude)])
                    .addTo(adminMap)
                    .bindPopup('<strong>' + wp.place_name + '</strong><br>' + wp.category + ' (' + wp.district_or_park + ')');
            });

            var adminDropMarker = null;
            adminMap.on('click', function(e) {
                var lat = e.latlng.lat.toFixed(6);
                var lng = e.latlng.lng.toFixed(6);
                latInput.value = lat;
                lngInput.value = lng;

                if (adminDropMarker) {
                    adminMap.removeLayer(adminDropMarker);
                }
                adminDropMarker = L.marker([lat, lng])
                    .addTo(adminMap)
                    .bindPopup('New Pin GPS: ' + lat + ', ' + lng)
                    .openPopup();
            });
        } else {
            // Self-Contained Offline Uganda GPS Coordinate Plotter Fallback
            adminMapBox.innerHTML = '<canvas id="adminOfflineCanvas" width="900" height="350" style="width:100%; height:100%; cursor:crosshair; display:block;"></canvas>';
            var canvas = document.getElementById('adminOfflineCanvas');
            var selectedPt = null;

            function drawAdminGrid() {
                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#0b1310';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                ctx.strokeStyle = '#1f362d';
                ctx.lineWidth = 1;
                for (var gx = 0; gx < canvas.width; gx += 75) { ctx.beginPath(); ctx.moveTo(gx, 0); ctx.lineTo(gx, canvas.height); ctx.stroke(); }
                for (var gy = 0; gy < canvas.height; gy += 70) { ctx.beginPath(); ctx.moveTo(0, gy); ctx.lineTo(canvas.width, gy); ctx.stroke(); }

                ctx.fillStyle = '#00f59b';
                ctx.font = 'bold 13px Arial';
                ctx.fillText('ADMIN GPS PIN MAP — Click Anywhere to Capture Coordinates for Hotels, Tour, Camping & Landing Sites', 16, 24);

                function toXY(lat, lng) {
                    return {
                        x: ((lng - 29.5) / 5.5) * canvas.width,
                        y: ((4.2 - lat) / 5.7) * canvas.height
                    };
                }

                existingAdminPins.forEach(function(wp) {
                    var pt = toXY(parseFloat(wp.latitude), parseFloat(wp.longitude));
                    ctx.fillStyle = '#38bdf8';
                    ctx.beginPath(); ctx.arc(pt.x, pt.y, 5, 0, Math.PI * 2); ctx.fill();
                    ctx.fillStyle = '#9eb3aa';
                    ctx.font = '11px Arial';
                    ctx.fillText(wp.place_name + ' [' + wp.category + ']', pt.x + 8, pt.y + 4);
                });

                if (selectedPt) {
                    var spt = toXY(selectedPt.lat, selectedPt.lng);
                    ctx.fillStyle = '#ffb703';
                    ctx.beginPath(); ctx.arc(spt.x, spt.y, 8, 0, Math.PI * 2); ctx.fill();
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 12px Arial';
                    ctx.fillText('NEW PIN (' + selectedPt.lat + ', ' + selectedPt.lng + ')', spt.x + 11, spt.y - 6);
                }
            }

            canvas.addEventListener('click', function(e) {
                var rect = canvas.getBoundingClientRect();
                var xRatio = (e.clientX - rect.left) / rect.width;
                var yRatio = (e.clientY - rect.top) / rect.height;
                var lat = parseFloat((4.2 - yRatio * 5.7).toFixed(6));
                var lng = parseFloat((29.5 + xRatio * 5.5).toFixed(6));
                latInput.value = lat;
                lngInput.value = lng;
                selectedPt = { lat: lat, lng: lng };
                drawAdminGrid();
            });

            drawAdminGrid();
        }
    });
    </script>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
