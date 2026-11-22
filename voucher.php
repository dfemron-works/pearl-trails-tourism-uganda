<?php
require 'db.php';
require_once 'currency_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$backUrl = ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'my_bookings.php';

if ($bookingId <= 0) {
    header("Location: " . $backUrl);
    exit;
}

if ($_SESSION['role'] === 'admin') {
    $stmt = $pdo->prepare("
        SELECT b.*, u.full_name, u.email, u.mobile_contact, u.nationality, u.id_number,
               t.title, t.destination, t.duration_days
        FROM bookings b
        INNER JOIN users u ON b.user_id = u.user_id
        INNER JOIN tour_packages t ON b.package_id = t.package_id
        WHERE b.booking_id = ?
        LIMIT 1
    ");
    $stmt->execute([$bookingId]);
} else {
    $stmt = $pdo->prepare("
        SELECT b.*, u.full_name, u.email, u.mobile_contact, u.nationality, u.id_number,
               t.title, t.destination, t.duration_days
        FROM bookings b
        INNER JOIN users u ON b.user_id = u.user_id
        INNER JOIN tour_packages t ON b.package_id = t.package_id
        WHERE b.booking_id = ? AND b.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$bookingId, (int)$_SESSION['user_id']]);
}

$voucher = $stmt->fetch();
if (!$voucher) {
    die("Safari E-Ticket #$bookingId not found or you do not have permission to view it.");
}

// Fetch all non-rejected payment records for THIS specific booking_id
$payStmt = $pdo->prepare("
    SELECT payment_method, transaction_reference, amount_paid_usd, amount_in_currency, payment_status, paid_at 
    FROM payments 
    WHERE booking_id = ? AND payment_status != 'Rejected' 
    ORDER BY paid_at DESC
");
$payStmt->execute([$bookingId]);
$paymentList = $payStmt->fetchAll();

$paidUsd = 0.0;
foreach ($paymentList as $pItem) {
    $paidUsd += (float)$pItem['amount_paid_usd'];
}

// Fixed: Index row  of $paymentList for the latest payment timestamp
$latestPaidTime = (!empty($paymentList) && !empty($paymentList['paid_at']))
    ? date('d M Y, h:i:s A', strtotime($paymentList['paid_at']))
    : 'Awaiting Payment Submission';

$totalUsd = (float)$voucher['total_cost'];
$balanceUsd = max(0, $totalUsd - $paidUsd);

$verifyCode = "PTU-TKT-" . str_pad((string)$bookingId, 5, '0', STR_PAD_LEFT) . "-" . strtoupper(substr(md5($voucher['email'] . $bookingId), 0, 6));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Safari E-Ticket #<?= $bookingId ?> - Pearl Trails Tours &amp; Travel Ug Ltd</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .ticket-box {
            position: relative;
            max-width: 800px;
            margin: 28px auto;
            background: #13201b;
            border: 2px solid var(--accent-neon);
            border-radius: 14px;
            padding: 34px 28px 28px;
        }
        .voucher-close-tab {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--danger-neon);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 5px 11px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            line-height: 1;
            transition: opacity 0.2s ease;
        }
        .voucher-close-tab:hover {
            opacity: 0.85;
        }
        .ticket-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px dashed var(--border-color);
            padding-bottom: 16px;
            padding-right: 34px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .ticket-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(225px, 1fr));
            gap: 14px;
            margin: 18px 0;
        }
        .ticket-cell {
            background: #0b1310;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        .ticket-cell small { color: var(--text-muted); display: block; font-size: 11px; text-transform: uppercase; margin-bottom: 3px; }
        .ticket-cell strong { color: var(--text-light); font-size: 15px; }
        .qr-stamp {
            background: #ffffff;
            padding: 8px;
            border-radius: 8px;
            border: 2px solid var(--accent-neon);
            display: inline-block;
        }
        .voucher-bottom-actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            margin-top: 22px;
            flex-wrap: wrap;
        }
        @media print {
            body { background: #fff !important; color: #000 !important; }
            header, .no-print, .voucher-close-tab, .voucher-bottom-actions, .site-footer, #safariChatToggle, #safariChatBox, #settingsModal, #signLanguageBar { display: none !important; }
            .ticket-box { border: 2px solid #000; background: #fff !important; color: #000 !important; box-shadow: none !important; margin: 0 auto; }
            .ticket-cell { background: #f8f9fa !important; border: 1px solid #ccc !important; }
            .ticket-cell strong, .ticket-cell small, td, th { color: #000 !important; }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="ticket-box">
    <!-- Top-Right Close 'X' Tab -->
    <a href="<?= htmlspecialchars($backUrl) ?>" onclick="if(window.opener){window.close(); return false;}" class="voucher-close-tab no-print" title="Close Voucher">✕</a>

    <div class="ticket-top">
        <div>
            <span style="color:var(--accent-neon); font-weight:bold; font-size:12px; letter-spacing:1.5px;">OFFICIAL SAFARI BOARDING VOUCHER &amp; RECEIPT</span>
            <h2 style="margin:4px 0;">Pearl Trails Tours &amp; Travel Ug Ltd</h2>
            <p style="margin:0; font-size:13px; color:var(--text-muted);">Plot 14, Kampala–Jinja Highway, Nagojje, Mukono District, Uganda &bull; +256 772 845 910</p>
        </div>
        <div style="text-align:center;">
            <div class="qr-stamp">
                <svg width="88" height="88" viewBox="0 0 88 88" xmlns="http://w3.org">
                    <rect width="88" height="88" fill="#ffffff"/>
                    <rect x="6" y="6" width="24" height="24" fill="none" stroke="#000" stroke-width="4"/>
                    <rect x="12" y="12" width="12" height="12" fill="#000"/>
                    <rect x="58" y="6" width="24" height="24" fill="none" stroke="#000" stroke-width="4"/>
                    <rect x="64" y="12" width="12" height="12" fill="#000"/>
                    <rect x="6" y="58" width="24" height="24" fill="none" stroke="#000" stroke-width="4"/>
                    <rect x="12" y="64" width="12" height="12" fill="#000"/>
                    <rect x="38" y="8" width="6" height="18" fill="#000"/>
                    <rect x="38" y="34" width="16" height="6" fill="#000"/>
                    <rect x="60" y="38" width="20" height="8" fill="#000"/>
                    <rect x="38" y="50" width="8" height="28" fill="#000"/>
                    <rect x="54" y="56" width="12" height="12" fill="#000"/>
                    <rect x="72" y="64" width="10" height="18" fill="#000"/>
                </svg>
            </div>
            <div style="font-size:11px; color:var(--accent-neon); margin-top:4px; font-weight:bold;"><?= htmlspecialchars($verifyCode) ?></div>
        </div>
    </div>

    <div class="ticket-grid">
        <div class="ticket-cell"><small>Passenger / Tourist Name</small><strong><?= htmlspecialchars($voucher['full_name']) ?></strong></div>
        <div class="ticket-cell"><small>Nationality &amp; ID / Passport</small><strong><?= htmlspecialchars($voucher['nationality']) ?> (<?= htmlspecialchars($voucher['id_number'] ?? 'N/A') ?>)</strong></div>
        <div class="ticket-cell"><small>Contact Phone &amp; Email</small><strong><?= htmlspecialchars($voucher['mobile_contact'] ?? $voucher['email']) ?></strong></div>
        <div class="ticket-cell"><small>Safari Package</small><strong><?= htmlspecialchars($voucher['title']) ?></strong></div>
        <div class="ticket-cell"><small>Destination &amp; Duration</small><strong><?= htmlspecialchars($voucher['destination']) ?> (<?= (int)$voucher['duration_days'] ?> Days)</strong></div>
        <div class="ticket-cell"><small>Scheduled Departure &amp; Guests</small><strong><?= htmlspecialchars($voucher['travel_date']) ?> &bull; <?= (int)$voucher['num_guests'] ?> Guest(s)</strong></div>
        <div class="ticket-cell"><small>Total Package Cost</small><strong><?= formatMoney($totalUsd) ?></strong></div>
        <div class="ticket-cell"><small>Amount Paid &amp; Balance Due</small><strong style="color:var(--accent-neon);">Paid: <?= formatMoney($paidUsd) ?> | Bal: <?= formatMoney($balanceUsd) ?></strong></div>
        <div class="ticket-cell"><small>Latest Payment Date &amp; Time</small><strong style="color:#ffb703;"><?= htmlspecialchars($latestPaidTime) ?></strong></div>
    </div>
    <?php if (!empty($voucher['route_summary']) && (float)($voucher['total_distance_km'] ?? 0) > 0): ?>
        <div style="background:#0b1310; border:1px solid var(--accent-neon); border-radius:10px; padding:14px; margin:14px 0;">
            <h4 style="color:var(--accent-neon); margin:0 0 8px; font-size:14px;">🗺️ Mapped Safari Route &amp; Vehicle Hire Breakdown</h4>
            <p style="margin:0 0 10px; font-size:13.5px; color:var(--text-light);">
                <strong>Itinerary Waypoints:</strong> <?= htmlspecialchars($voucher['route_summary']) ?>
            </p>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:10px; font-size:13px;">
                <div>
                    <small style="color:var(--text-muted); display:block;">TOTAL DISTANCE</small>
                    <strong style="color:#ffb703;"><?= number_format((float)$voucher['total_distance_km'], 1) ?> km</strong>
                </div>
                <div>
                    <small style="color:var(--text-muted); display:block;">EST. DRIVING TIME</small>
                    <strong><?= number_format((float)$voucher['total_travel_hours'], 1) ?> Hours</strong>
                </div>
                <div>
                    <small style="color:var(--text-muted); display:block;">DAYS IN THE FIELD</small>
                    <strong><?= (int)$voucher['field_days'] ?> Day(s)</strong>
                </div>
                <div>
                    <small style="color:var(--text-muted); display:block;">HIRE RATE / KM</small>
                    <strong><?= formatMoney((float)$voucher['hire_rate_per_km_usd']) ?> / km</strong>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($paymentList)): ?>
        <h4 style="color:var(--accent-neon); margin: 14px 0 8px;">Verified Electronic Payment Breakdown</h4>
        <table style="margin-bottom: 14px;">
            <tr>
                <th>Payment Channel</th>
                <th>Transaction Ref</th>
                <th>Amount Paid</th>
                <th>Date &amp; Time Paid</th>
                <th>Status</th>
            </tr>
            <?php foreach ($paymentList as $payRow): ?>
            <tr>
                <td><strong><?= htmlspecialchars($payRow['payment_method']) ?></strong></td>
                <td><code><?= htmlspecialchars($payRow['transaction_reference']) ?></code></td>
                <td><?= htmlspecialchars($payRow['amount_in_currency']) ?></td>
                <td><?= htmlspecialchars(date('d M Y, h:i:s A', strtotime($payRow['paid_at']))) ?></td>
                <td><strong><?= htmlspecialchars($payRow['payment_status']) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <p style="font-size:12.5px; color:var(--text-muted); border-top:1px solid var(--border-color); padding-top:12px;">
        <strong>Boarding Note:</strong> Present this official digital or printed E-Ticket alongside your National ID or Passport to your assigned Pearl Trails Tour Guide at departure.
    </p>

    <div class="voucher-bottom-actions no-print">
        <a href="<?= htmlspecialchars($backUrl) ?>" onclick="if(window.opener){window.close(); return false;}" class="btn btn-edit" style="min-width: 140px; text-align: center;">
            &larr; Back
        </a>
        <button type="button" onclick="window.print()" style="min-width: 170px;">
            💾 Save / Print Voucher
        </button>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>

