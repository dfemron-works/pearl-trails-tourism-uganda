<?php
require 'db.php';
require_once 'currency_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $cancel_id = (int)$_POST['booking_id'];
    $reason = trim($_POST['cancel_reason']);
    if ($reason === '') {
        $reason = "Cancelled by tourist";
    }
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'Cancelled', status_reason = ? WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$reason, $cancel_id, $_SESSION['user_id']]);
    $feedback = "Booking #" . $cancel_id . " has been cancelled.";
}

// Fetch Logged-In Tourist's Bookings along with Total Amount Paid per Booking
$stmt = $pdo->prepare("
    SELECT b.*, t.title, t.destination,
           COALESCE((SELECT SUM(p.amount_paid_usd) FROM payments p WHERE p.booking_id = b.booking_id AND p.payment_status != 'Rejected'), 0) AS paid_usd
    FROM bookings b 
    JOIN tour_packages t ON b.package_id = t.package_id 
    WHERE b.user_id = ? 
    ORDER BY b.booked_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings &amp; E-Tickets - Pearl Trails Tours &amp; Travel Ug Ltd</title>
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
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h2>My Safari Bookings, Payments &amp; Official E-Tickets</h2>
        <a href="index.php" class="btn">+ Book Another Safari</a>
    </div>

    <?php if ($feedback): ?>
        <div class="alert"><?= htmlspecialchars($feedback) ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Safari Package</th>
            <th>Travel Date &amp; Guests</th>
            <th>Total Cost &amp; Balance (<?= htmlspecialchars($activeCurrency) ?>)</th>
            <th>Status &amp; Note</th>
            <th>Pay, E-Ticket &amp; Cancel</th>
        </tr>
        <?php if (empty($bookings)): ?>
            <tr>
                <td colspan="5" style="text-align:center;">
                    You have not booked any safaris yet. <a href="index.php" style="color:var(--accent-neon);">Explore Packages</a>.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($bookings as $b): ?>
            <?php
                $totalUsd = (float)$b['total_cost'];
                $paidUsd = (float)$b['paid_usd'];
                $balanceUsd = max(0, $totalUsd - $paidUsd);
            ?>
            <tr>
                <td>
                    <strong>#<?= (int)$b['booking_id'] ?> — <?= htmlspecialchars($b['title']) ?></strong><br>
                    <small style="color:var(--text-muted);"><?= htmlspecialchars($b['destination']) ?></small>
                </td>
                <td>
                    <?= htmlspecialchars($b['travel_date']) ?><br>
                    <small style="color:var(--text-muted);"><?= (int)$b['num_guests'] ?> Guests</small>
                </td>
                <td>
                    <strong>Total: <?= formatMoney($totalUsd) ?></strong><br>
                    <small style="color:#ffb703;">Paid: <?= formatMoney($paidUsd) ?></small><br>
                    <small style="color:var(--accent-neon); font-weight:bold;">Balance: <?= formatMoney($balanceUsd) ?></small>
                </td>
                <td>
                    <span class="badge badge-<?= htmlspecialchars($b['status']) ?>"><?= htmlspecialchars($b['status']) ?></span><br>
                    <small style="color:var(--text-muted);"><?= htmlspecialchars($b['status_reason'] ?? '') ?></small>
                </td>
                <td>
                    <?php if ($b['status'] !== 'Cancelled'): ?>
                        <div style="display:flex; flex-direction:column; gap:6px;">
                            <a href="payment_checkout.php?booking_id=<?= (int)$b['booking_id'] ?>" class="btn" style="text-align:center; padding:6px 10px;">
                                <?= $balanceUsd > 0.01 ? '💳 Pay Deposit / Full' : '🧾 View Receipts' ?>
                            </a>

                            <a href="voucher.php?booking_id=<?= (int)$b['booking_id'] ?>" class="btn btn-edit" style="text-align:center; padding:6px 10px;">
                            🎟️ E-Ticket / Voucher
                            </a>

                            <?php if ($b['status'] === 'Pending' || $b['status'] === 'Confirmed'): ?>
                                <form method="POST" style="display:flex; gap:5px; margin:0;" onsubmit="return confirm('Confirm booking cancellation?');">
                                    <input type="hidden" name="booking_id" value="<?= (int)$b['booking_id'] ?>">
                                    <input type="text" name="cancel_reason" placeholder="Reason to cancel..." required style="margin:0; padding:5px; font-size:12px;">
                                    <button type="submit" name="cancel_booking" class="btn btn-danger" style="padding:5px 9px; font-size:12px;">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <span style="color:var(--text-muted);">Cancelled</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
