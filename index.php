<?php
require 'db.php';
require_once 'currency_helper.php';
$feedback = '';

// Load all active Ugandan Public Holidays into an associative map ['YYYY-MM-DD' => 'Holiday Name']
$holidayRows = $pdo->query("SELECT holiday_date, holiday_name FROM uganda_holidays WHERE is_active = 1 ORDER BY holiday_date ASC")->fetchAll();
$ugHolidaysMap = [];
foreach ($holidayRows as $hr) {
    $ugHolidaysMap[$hr['holiday_date']] = $hr['holiday_name'];
}

// Helper: Check if a YYYY-MM-DD date falls on a Weekend (Saturday=6, Sunday=7)
function isWeekendDate(string $dateStr): bool {
    $dayOfWeek = (int)date('N', strtotime($dateStr));
    return ($dayOfWeek === 6 || $dayOfWeek === 7);
}

// Handle Tour Booking Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_package'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $package_id = (int)$_POST['package_id'];
    $travel_date = $_POST['travel_date'];
    $num_guests = (int)$_POST['num_guests'];

    $pkgStmt = $pdo->prepare("SELECT price_usd, max_capacity, discount_percent FROM tour_packages WHERE package_id = ?");
    $pkgStmt->execute([$package_id]);
    $pkg = $pkgStmt->fetch();

    if (!$pkg) {
        $feedback = "Error: Selected tour package does not exist.";
    } elseif ($travel_date <= date('Y-m-d')) {
        $feedback = "Error: Travel date must be in the future.";
    } elseif ($num_guests < 1 || $num_guests > $pkg['max_capacity']) {
        $feedback = "Error: Guests must be between 1 and " . $pkg['max_capacity'] . ".";
    } else {
        $discount = (int)($pkg['discount_percent'] ?? 0);
        $isUgHoliday = isset($ugHolidaysMap[$travel_date]);
        $isWeekend = isWeekendDate($travel_date);

        // Strictly apply discount ONLY on Weekends or Ugandan Public Holidays
        $discountApplies = ($discount > 0 && ($isUgHoliday || $isWeekend));

        $effectiveUnitUsd = $discountApplies
            ? (float)$pkg['price_usd'] * (1 - ($discount / 100))
            : (float)$pkg['price_usd'];

        $total_cost_usd = $effectiveUnitUsd * $num_guests;

        if ($discountApplies) {
            $occasion = $isUgHoliday ? $ugHolidaysMap[$travel_date] : date('l', strtotime($travel_date)) . ' Weekend';
            $note = "Booked under {$discount}% Offer ({$occasion})";
        } elseif ($discount > 0) {
            $note = "Weekday booking (Standard rate applied; {$discount}% offer valid only on Weekends & UG Holidays)";
        } else {
            $note = "Awaiting initial review";
        }

        $ins = $pdo->prepare("INSERT INTO bookings (user_id, package_id, travel_date, num_guests, total_cost, status_reason) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$_SESSION['user_id'], $package_id, $travel_date, $num_guests, $total_cost_usd, $note]);

        $newBookingId = (int)$pdo->lastInsertId();
        header("Location: payment_checkout.php?booking_id=" . $newBookingId);
        exit;
    }
}

// Search & Offers Filter
$search = trim($_GET['search'] ?? '');
$onlyOffers = (isset($_GET['offers']) && $_GET['offers'] === '1');

if ($onlyOffers && $search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM tour_packages WHERE discount_percent > 0 AND (title LIKE ? OR destination LIKE ?) ORDER BY discount_percent DESC");
    $stmt->execute(["%$search%", "%$search%"]);
} elseif ($onlyOffers) {
    $stmt = $pdo->query("SELECT * FROM tour_packages WHERE discount_percent > 0 ORDER BY discount_percent DESC");
} elseif ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM tour_packages WHERE title LIKE ? OR destination LIKE ? ORDER BY package_id DESC");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM tour_packages ORDER BY package_id DESC");
}
$packages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $onlyOffers ? 'Weekend & Ugandan Holiday Offers' : 'Pearl Trails Tours & Travel Ug Ltd - Safari Packages' ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .safari-track-lane {
            flex: 1;
            min-width: 260px;
            height: 54px;
            background: linear-gradient(180deg, rgba(0, 245, 155, 0.05) 0%, rgba(11, 19, 16, 0.85) 82%, #243b32 82%, #192b24 100%);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
        }
        .safari-scenery {
            position: absolute;
            bottom: 8px;
            width: 200%;
            display: flex;
            justify-content: space-around;
            font-size: 14px;
            opacity: 0.45;
            animation: sceneryScroll 14s linear infinite;
            pointer-events: none;
        }
        .landcruiser-rig {
            position: absolute;
            bottom: 4px;
            left: -230px;
            animation: driveAcrossSafari 11s linear infinite, suspensionBounce 0.45s ease-in-out infinite alternate;
            will-change: transform, left;
        }
        .wheel-spin {
            transform-origin: center;
            transform-box: fill-box;
            animation: spinTire 0.6s linear infinite;
        }
        @keyframes driveAcrossSafari {
            0%   { left: -230px; }
            100% { left: 105%; }
        }
        @keyframes suspensionBounce {
            0%   { transform: translateY(0px); }
            100% { transform: translateY(-1.8px); }
        }
        @keyframes spinTire {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes sceneryScroll {
            0%   { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <!-- ROW 1: Page Heading on Left + View Special Offers & Exchange Rate on Extreme Right -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px; width:100%;">
        <h2 style="margin:0;"><?= $onlyOffers ? '🔥 Weekend & Ugandan Public Holiday Safari Offers' : 'Explore Uganda Safari Packages' ?></h2>

        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-left:auto;">
            <?php if ($onlyOffers): ?>
                <a href="index.php" class="btn btn-edit" style="margin:0;">View All Packages</a>
            <?php else: ?>
                <a href="index.php?offers=1" class="btn" style="margin:0;">View Special Offers</a>
            <?php endif; ?>

            <span style="font-size:13px; color:var(--accent-neon); background:var(--accent-neon-soft); padding:6px 12px; border-radius:20px; white-space:nowrap;">
                1 USD = <?= number_format($currentRate, 2) ?> <?= htmlspecialchars($activeCurrency) ?>
            </span>
        </div>
    </div>

    <!-- ROW 2: Three Filter Buttons on Left + Extended Animated Safari Land Cruiser Bar on Right -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; width:100%;">
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <button type="button" class="btn" onclick="window.filterSafariCards('ALL')" style="margin:0;">🌍 All Safaris</button>
            <button type="button" class="btn btn-edit" onclick="window.filterSafariCards('OFFERS')" style="margin:0;">🔥 Weekend &amp; Holiday Offers</button>
            <button type="button" class="btn btn-edit" onclick="window.filterSafariCards('MULTI_DAY')" style="margin:0;">⛺ 3+ Days Expeditions</button>
        </div>

        <div class="safari-track-lane" title="Pearl Trails Tours & Travel Ug Ltd — On Safari">
            <div class="safari-scenery">
                <span>🌴</span><span>🌿</span><span>🦒</span><span>🌳</span><span>🌾</span>
                <span>🌴</span><span>🌿</span><span>🐘</span><span>🌳</span><span>🌾</span>
            </div>

            <div class="landcruiser-rig">
                <svg width="215" height="46" viewBox="0 0 215 46" xmlns="http://w3.org">
                    <circle cx="8" cy="34" r="3" fill="#9eb3aa" opacity="0.45"/>
                    <circle cx="2" cy="31" r="2" fill="#9eb3aa" opacity="0.3"/>

                    <rect x="46" y="3" width="96" height="3" rx="1.5" fill="#ffb703"/>
                    <line x1="52" y1="6" x2="52" y2="12" stroke="#9eb3aa" stroke-width="1.5"/>
                    <line x1="136" y1="6" x2="136" y2="12" stroke="#9eb3aa" stroke-width="1.5"/>

                    <circle cx="66" cy="9" r="3.2" fill="#f4a261"/>
                    <path d="M61 12 Q66 10 71 12" stroke="#00f59b" stroke-width="2" fill="none"/>
                    <circle cx="88" cy="8.5" r="3.2" fill="#e9c46a"/>
                    <rect x="91" y="7" width="5" height="3" rx="1" fill="#111"/>
                    <circle cx="112" cy="9" r="3.2" fill="#f4a261"/>

                    <rect x="18" y="16" width="6" height="16" rx="2" fill="#111" stroke="#00f59b" stroke-width="1"/>

                    <path d="M24 12 L144 12 L156 21 L194 23 Q200 24 200 30 L200 35 L24 35 Z" fill="#1b4332" stroke="#00f59b" stroke-width="1.4"/>

                    <rect x="32" y="14" width="24" height="8" rx="1.5" fill="#0b1310" stroke="#38bdf8" stroke-width="0.8"/>
                    <rect x="60" y="14" width="26" height="8" rx="1.5" fill="#0b1310" stroke="#38bdf8" stroke-width="0.8"/>
                    <rect x="90" y="14" width="26" height="8" rx="1.5" fill="#0b1310" stroke="#38bdf8" stroke-width="0.8"/>
                    <polygon points="120,14 141,14 150,22 120,22" fill="#0b1310" stroke="#38bdf8" stroke-width="0.8"/>
                    <circle cx="131" cy="18" r="2.7" fill="#e9c46a"/>

                    <rect x="28" y="24" width="166" height="8" rx="2" fill="#0b1310" opacity="0.85"/>
                    <text x="111" y="29.8" fill="#00f59b" font-family="Arial, sans-serif" font-size="6.4" font-weight="bold" text-anchor="middle" letter-spacing="0.3">
                        PEARL TRAILS TOURS &amp; TRAVEL UG LTD
                    </text>

                    <polygon points="200,26 215,22 215,33 200,30" fill="#ffb703" opacity="0.45"/>
                    <circle cx="199" cy="28" r="2" fill="#ffb703"/>

                    <g class="wheel-spin">
                        <circle cx="54" cy="36" r="7.5" fill="#0b1310" stroke="#00f59b" stroke-width="2"/>
                        <circle cx="54" cy="36" r="3" fill="#ffb703"/>
                        <line x1="54" y1="29" x2="54" y2="43" stroke="#9eb3aa" stroke-width="1"/>
                        <line x1="47" y1="36" x2="61" y2="36" stroke="#9eb3aa" stroke-width="1"/>
                    </g>
                    <g class="wheel-spin">
                        <circle cx="166" cy="36" r="7.5" fill="#0b1310" stroke="#00f59b" stroke-width="2"/>
                        <circle cx="166" cy="36" r="3" fill="#ffb703"/>
                        <line x1="166" y1="29" x2="166" y2="43" stroke="#9eb3aa" stroke-width="1"/>
                        <line x1="159" y1="36" x2="173" y2="36" stroke="#9eb3aa" stroke-width="1"/>
                    </g>
                </svg>
            </div>
        </div>
    </div>

    <?php if ($feedback): ?>
        <div class="alert <?= strpos($feedback, 'Error') === 0 ? 'error' : '' ?>">
            <?= htmlspecialchars($feedback) ?>
        </div>
    <?php endif; ?>

    <form method="GET" style="display:flex; gap:10px; margin:18px 0;">
        <?php if ($onlyOffers): ?><input type="hidden" name="offers" value="1"><?php endif; ?>
        <input type="text" name="search" placeholder="Search by destination or safari name..." value="<?= htmlspecialchars($search) ?>" style="margin:0;">
        <button type="submit">Search</button>
    </form>

    <div class="grid">
        <?php if (empty($packages)): ?>
            <p>No active offers match your filter right now. <a href="index.php" style="color:var(--accent-neon);">Browse all packages</a>.</p>
        <?php else: ?>
            <?php foreach ($packages as $index => $p): ?>
                <?php
                    $discount = (int)($p['discount_percent'] ?? 0);
                    $hasOffer = $discount > 0;
                    $originalUsd = (float)$p['price_usd'];
                    $discountedUsd = $originalUsd * (1 - ($discount / 100));
                    $baseConverted = $originalUsd * $currentRate;
                    $promoConverted = $discountedUsd * $currentRate;
                ?>
                <div class="tier-card <?= $hasOffer ? 'on-offer' : ($index === 0 ? 'popular' : '') ?>" data-days="<?= (int)$p['duration_days'] ?>">
                    <?php if ($hasOffer): ?>
                        <div class="offer-badge">
                            <?= $discount ?>% OFF • WEEKENDS &amp; 🇺🇬 HOLIDAYS
                        </div>
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                    <div class="price">
                        <?php if ($hasOffer): ?>
                            <span class="old-price"><?= formatMoney($originalUsd) ?></span>
                        <?php endif; ?>
                        <?= formatMoney($discountedUsd) ?> <span>/ person</span>
                    </div>

                    <ul class="tier-features">
                        <li>Destination: <?= htmlspecialchars($p['destination']) ?></li>
                        <li>Duration: <?= (int)$p['duration_days'] ?> Days Guided Safari</li>
                    <ul class="tier-features">
                        <li>Destination: <?= htmlspecialchars($p['destination']) ?></li>
                        <li>Duration: <?= (int)$p['duration_days'] ?> Days Guided Safari</li>
                        <li>Group Limit: Up to <?= (int)$p['max_capacity'] ?> Guests</li>
                        <?php if ($hasOffer): ?>
                            <li style="color:#ffb703; font-weight:600;">
                                Valid Strictly on Weekends (Sat–Sun) &amp; Uganda's Public Holidays
                            </li>
                        <?php endif; ?>
                        <li><?= htmlspecialchars($p['description']) ?></li>
                    </ul>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'tourist'): ?>
                        <form method="POST" class="booking-form"
                              data-base-converted="<?= htmlspecialchars($baseConverted) ?>"
                              data-promo-converted="<?= htmlspecialchars($promoConverted) ?>"
                              data-discount="<?= $discount ?>"
                              data-symbol="<?= htmlspecialchars($currencySymbol) ?>"
                              data-currency="<?= htmlspecialchars($activeCurrency) ?>"
                              style="text-align:left;">
                            <input type="hidden" name="package_id" value="<?= (int)$p['package_id'] ?>">
                            
                            <label>Travel Date</label>
                            <input type="date" name="travel_date" class="date-input" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                            <small class="holiday-status-note" style="display:block; margin-top:-8px; margin-bottom:10px; font-weight:600;"></small>
                            
                            <label>Number of Guests</label>
                            <input type="number" name="num_guests" class="guest-input" min="1" max="<?= (int)$p['max_capacity'] ?>" value="1" required>
                            
                            <p style="background:var(--accent-neon-soft); color:var(--accent-neon); padding:10px; border-radius:6px; font-weight:bold; text-align:center;">
                                Estimated Total: <span class="live-total"><?= formatMoney($originalUsd) ?></span>
                            </p>
                            
                            <button type="submit" name="book_package" style="width:100%;">
                                Book Safari &amp; Proceed to Pay
                            </button>
                        </form>
                    <?php elseif (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="btn" style="width:100%; box-sizing:border-box;">Login to Book</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
const ugandaHolidays = <?= json_encode($ugHolidaysMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

window.filterSafariCards = function(mode) {
    var searchInput = document.querySelector('input[name="search"]');
    if (mode === 'ALL' && searchInput) {
        searchInput.value = '';
    }

    var cards = document.querySelectorAll('.tier-card');
    cards.forEach(function(card) {
        var isOffer = card.classList.contains('on-offer');
        var days = parseInt(card.getAttribute('data-days') || '1', 10);

        if (mode === 'ALL') {
            card.style.display = '';
        } else if (mode === 'OFFERS') {
            card.style.display = isOffer ? '' : 'none';
        } else if (mode === 'MULTI_DAY') {
            card.style.display = (days >= 3) ? '' : 'none';
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    var searchBox = document.querySelector('input[name="search"]');
    if (searchBox) {
        searchBox.addEventListener('input', function() {
            var q = this.value.toLowerCase().trim();
            document.querySelectorAll('.tier-card').forEach(function(card) {
                var text = (card.innerText || '').toLowerCase();
                card.style.display = text.includes(q) ? '' : 'none';
            });
        });
    }

    const bookingForms = document.querySelectorAll('.booking-form');
    bookingForms.forEach(form => {
        const basePrice = parseFloat(form.dataset.baseConverted);
        const promoPrice = parseFloat(form.dataset.promoConverted);
        const discount = parseInt(form.dataset.discount, 10);
        const symbol = form.dataset.symbol;
        const currency = form.dataset.currency;

        const dateInput = form.querySelector('.date-input');
        const guestInput = form.querySelector('.guest-input');
        const totalDisplay = form.querySelector('.live-total');
        const noteDisplay = form.querySelector('.holiday-status-note');

        const wholeCurrencies = ['UGX', 'TZS', 'RWF'];
        const decimals = wholeCurrencies.includes(currency) ? 0 : 2;

        function isWeekend(dateStr) {
            if (!dateStr) return false;
            const parts = dateStr.split('-');
            const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            const day = d.getDay();
            return day === 0 || day === 6;
        }

        function recalculate() {
            const selectedDate = dateInput.value;
            const guests = parseInt(guestInput.value, 10) || 1;
            const holidayName = ugandaHolidays[selectedDate] || null;
            const weekend = isWeekend(selectedDate);

            let activeUnitPrice = basePrice;

            if (discount > 0) {
                if (selectedDate && (holidayName || weekend)) {
                    activeUnitPrice = promoPrice;
                    noteDisplay.style.color = '#00f59b';
                    const occasion = holidayName ? `🇺🇬 ${holidayName}` : 'Weekend Special';
                    noteDisplay.textContent = `✓ ${occasion}! ${discount}% Offer Applied.`;
                } else if (selectedDate) {
                    activeUnitPrice = basePrice;
                    noteDisplay.style.color = '#ffb703';
                    noteDisplay.textContent = `Weekday selected (Standard rate). Pick a Weekend (Sat/Sun) or UG Public Holiday for ${discount}% OFF.`;
                } else {
                    activeUnitPrice = basePrice;
                    noteDisplay.style.color = '#9eb3aa';
                    noteDisplay.textContent = `Pick a Weekend (Sat/Sun) or Ugandan Public Holiday to unlock ${discount}% OFF.`;
                }
            }

            const total = activeUnitPrice * Math.max(1, guests);
            totalDisplay.textContent = symbol + total.toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }

        dateInput.addEventListener('change', recalculate);
        guestInput.addEventListener('input', recalculate);
        recalculate();
    });
});
</script>
<?php include 'footer.php'; ?>
</body>
</html>