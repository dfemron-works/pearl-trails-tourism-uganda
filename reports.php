<?php
require 'db.php';
require_once 'currency_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$statusFilter = $_GET['status'] ?? 'ALL';
$allowedStatuses = ['ALL', 'Pending', 'Confirmed', 'Concluded', 'Cancelled'];
if (!in_array($statusFilter, $allowedStatuses, true)) {
    $statusFilter = 'ALL';
}

// 1. MULTI-PERIOD TRAFFIC, LOGIN & REGISTRATION ANALYTICS
$trafficStats = $pdo->query("
    SELECT
        SUM(CASE WHEN event_type = 'PAGE_VISIT' AND created_at >= NOW() - INTERVAL 1 HOUR THEN 1 ELSE 0 END) AS visits_hour,
        SUM(CASE WHEN event_type = 'PAGE_VISIT' AND created_at >= CURDATE() THEN 1 ELSE 0 END) AS visits_day,
        SUM(CASE WHEN event_type = 'PAGE_VISIT' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS visits_week,
        SUM(CASE WHEN event_type = 'PAGE_VISIT' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS visits_month,
        SUM(CASE WHEN event_type = 'PAGE_VISIT' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 365 DAY) THEN 1 ELSE 0 END) AS visits_year,
        SUM(CASE WHEN event_type = 'LOGIN' AND created_at >= NOW() - INTERVAL 1 HOUR THEN 1 ELSE 0 END) AS logins_hour,
        SUM(CASE WHEN event_type = 'LOGIN' AND created_at >= CURDATE() THEN 1 ELSE 0 END) AS logins_day,
        SUM(CASE WHEN event_type = 'LOGIN' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS logins_week,
        SUM(CASE WHEN event_type = 'LOGIN' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS logins_month,
        SUM(CASE WHEN event_type = 'LOGIN' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 365 DAY) THEN 1 ELSE 0 END) AS logins_year,
        SUM(CASE WHEN event_type = 'REGISTER' AND created_at >= NOW() - INTERVAL 1 HOUR THEN 1 ELSE 0 END) AS reg_hour,
        SUM(CASE WHEN event_type = 'REGISTER' AND created_at >= CURDATE() THEN 1 ELSE 0 END) AS reg_day,
        SUM(CASE WHEN event_type = 'REGISTER' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS reg_week,
        SUM(CASE WHEN event_type = 'REGISTER' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS reg_month,
        SUM(CASE WHEN event_type = 'REGISTER' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 365 DAY) THEN 1 ELSE 0 END) AS reg_year
    FROM system_logs
")->fetch();

// 2. USER ACCOUNT STATUS SUMMARY
$userStats = $pdo->query("
    SELECT
        COUNT(*) AS total_users,
        SUM(CASE WHEN role = 'tourist' THEN 1 ELSE 0 END) AS total_tourists,
        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) AS total_admins,
        SUM(CASE WHEN account_status = 'Active' THEN 1 ELSE 0 END) AS active_users,
        SUM(CASE WHEN account_status = 'Flagged' THEN 1 ELSE 0 END) AS flagged_users,
        SUM(CASE WHEN account_status = 'Suspended' THEN 1 ELSE 0 END) AS suspended_users
    FROM users
")->fetch();

// 3. BOOKING & FINANCIAL KPI TOTALS & AVERAGES
$kpi = $pdo->query("
    SELECT 
        COUNT(*) AS total_bookings,
        SUM(CASE WHEN status = 'Confirmed' THEN 1 ELSE 0 END) AS confirmed_count,
        SUM(CASE WHEN status = 'Concluded' THEN 1 ELSE 0 END) AS concluded_count,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_count,
        COALESCE(SUM(CASE WHEN status IN ('Confirmed', 'Concluded') THEN total_cost ELSE 0 END), 0) AS realized_revenue_usd,
        COALESCE(SUM(CASE WHEN status = 'Pending' THEN total_cost ELSE 0 END), 0) AS pending_revenue_usd,
        COALESCE(AVG(CASE WHEN status IN ('Confirmed', 'Concluded') THEN total_cost ELSE NULL END), 0) AS avg_confirmed_value_usd,
        COALESCE(AVG(total_cost), 0) AS avg_all_orders_value,
        COALESCE(AVG(num_guests), 0) AS avg_group_size
    FROM bookings
")->fetch();

// 4. FILTERED BOOKINGS LIST
if ($statusFilter === 'ALL') {
    $stmt = $pdo->query("
        SELECT b.booking_id, u.full_name, u.email, t.title, t.destination,
               b.travel_date, b.num_guests, b.total_cost, b.status, b.status_reason
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN tour_packages t ON b.package_id = t.package_id
        ORDER BY b.booked_at DESC
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT b.booking_id, u.full_name, u.email, t.title, t.destination,
               b.travel_date, b.num_guests, b.total_cost, b.status, b.status_reason
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN tour_packages t ON b.package_id = t.package_id
        WHERE b.status = ?
        ORDER BY b.booked_at DESC
    ");
    $stmt->execute([$statusFilter]);
}
$rows = $stmt->fetchAll();

// 5. EXCEL / CSV EXPORT
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="pearl_trails_report_' . strtolower($statusFilter) . '_' . strtolower($activeCurrency) . '_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');

    fputcsv($out, ['PEARL TRAILS TOURS & TRAVEL UGANDA LTD - ANALYTICS REPORT (' . $activeCurrency . ')']);
    fputcsv($out, ['Period', 'Page Visits', 'User Logins', 'New Registrations']);
    fputcsv($out, ['Last 1 Hour', (int)$trafficStats['visits_hour'], (int)$trafficStats['logins_hour'], (int)$trafficStats['reg_hour']]);
    fputcsv($out, ['Today (Day)', (int)$trafficStats['visits_day'], (int)$trafficStats['logins_day'], (int)$trafficStats['reg_day']]);
    fputcsv($out, ['Last 7 Days (Week)', (int)$trafficStats['visits_week'], (int)$trafficStats['logins_week'], (int)$trafficStats['reg_week']]);
    fputcsv($out, ['Last 30 Days (Month)', (int)$trafficStats['visits_month'], (int)$trafficStats['logins_month'], (int)$trafficStats['reg_month']]);
    fputcsv($out, ['Last 365 Days (Year)', (int)$trafficStats['visits_year'], (int)$trafficStats['logins_year'], (int)$trafficStats['reg_year']]);
    fputcsv($out, []);

    fputcsv($out, ['Booking ID', 'Tourist Name', 'Email', 'Package', 'Destination', 'Travel Date', 'Guests', 'Total (' . $activeCurrency . ')', 'Status', 'Status Note']);
    foreach ($rows as $r) {
        $convertedVal = number_format((float)$r['total_cost'] * $currentRate, in_array($activeCurrency, ['UGX', 'TZS', 'RWF'], true) ? 0 : 2, '.', '');
        fputcsv($out, [
            $r['booking_id'], $r['full_name'], $r['email'], $r['title'],
            $r['destination'], $r['travel_date'], $r['num_guests'],
            $convertedVal, $r['status'], $r['status_reason']
        ]);
    }
    fclose($out);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Traffic, Users &amp; Financial Reports - Pearl Trails Tours &amp; Travel Uganda Ltd</title>
    <link rel="stylesheet" href="style.css">
        <style>
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 15px; margin: 18px 0; }
        .kpi-card { background: var(--bg-card); border: 1px solid var(--border-color); border-left: 4px solid var(--accent-neon); padding: 15px; border-radius: 8px; }
        .kpi-card.kpi-warn { border-left-color: var(--warn-neon); }
        .kpi-card.kpi-danger { border-left-color: var(--danger-neon); }
        .kpi-card h4 { margin: 0 0 6px; font-size: 12px; color: var(--text-muted); text-transform: uppercase; }
        .kpi-card p { margin: 0; font-size: 21px; font-weight: bold; color: var(--text-light); }
        .kpi-card small { display: block; margin-top: 4px; font-size: 12px; color: var(--accent-neon); }
        .filter-bar { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin: 16px 0; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-Confirmed { background: var(--accent-neon-soft); color: var(--accent-neon); }
        .badge-Concluded { background: rgba(56, 189, 248, 0.15); color: #38bdf8; }
        .badge-Pending { background: rgba(255, 183, 3, 0.15); color: var(--warn-neon); }
        .badge-Cancelled { background: rgba(255, 77, 109, 0.15); color: var(--danger-neon); }

        @media print {
            /* 1. Keep Top Land Cruiser Banner & Company Title as Official Letterhead */
            .top-safari-header-strip {
                display: flex !important;
                background: #f8f9fa !important;
                border: 2px solid #1b4332 !important;
                border-radius: 8px !important;
                margin-bottom: 16px !important;
                padding: 10px 16px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .brand-title-box { background: transparent !important; border-right: 2px solid #1b4332 !important; }
            .brand-title-box strong { color: #1b4332 !important; }
            .brand-title-box small { color: #8c6239 !important; }
            .savanna-runway { background: #eef6f1 !important; border: 1px solid #1b4332 !important; }
            .cruiser-hero-rig { animation: none !important; right: 24px !important; }

            /* 2. Keep Bottom Footer & Copyright Markers Visible */
            .site-footer {
                display: block !important;
                margin-top: 24px !important;
                padding-top: 10px !important;
                border-top: 2px solid #1b4332 !important;
                background: transparent !important;
                color: #111111 !important;
                text-align: center !important;
                font-size: 12px !important;
            }
            .site-footer p, .site-footer strong, .site-footer a {
                color: #111111 !important;
                text-decoration: none !important;
            }

            /* 3. Strictly Hide Navigation Tabs, Buttons, Filters, Charts & Floating Widgets */
            header, nav, .no-print, .filter-bar, button, a.btn, form,
            #safariChatToggle, #safariChatBox, #settingsModal, #signLanguageBar,
            #adminAlertDrawer, #liveAlertToast {
                display: none !important;
                visibility: hidden !important;
            }

            /* 4. Clean White-Paper Layout for KPIs & Tables */
            body { background: #ffffff !important; color: #000000 !important; }
            .container { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            .kpi-card { background: #ffffff !important; border: 1px solid #444 !important; box-shadow: none !important; }
            .kpi-card p, .kpi-card h4, .kpi-card small, h2, h3 { color: #000000 !important; }
            table, th, td { border: 1px solid #444 !important; color: #000000 !important; background: #ffffff !important; }
            th { background: #e9ecef !important; font-weight: bold !important; }
            .badge { border: 1px solid #333 !important; background: #f8f9fa !important; color: #000000 !important; }
        }
    </style>

</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h2>System Traffic, User Status &amp; Financial Reports (<?= htmlspecialchars($activeCurrency) ?>)</h2>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" onclick="window.print()" class="btn btn-edit no-print">🖨️ Print Analytics Report</button>
            <a href="?status=<?= urlencode($statusFilter) ?>&export=csv" class="btn no-print">📥 Export Report (CSV / Excel)</a>
	        <a href="weather_report.php" class="<?= $currentPage === 'weather_report.php' ? 'active' : '' ?>">⛈️ Weather Report</a>
        </div>
    </div>

    <!-- SECTION 1: SYSTEM TRAFFIC, LOGINS & REGISTRATIONS -->
    <h3>1. System Visits, Logins &amp; Registrations (Hour / Day / Week / Month / Year)</h3>
    <table>
        <tr>
            <th>Activity Metric</th>
            <th>Last 1 Hour</th>
            <th>Today (Day)</th>
            <th>Last 7 Days (Week)</th>
            <th>Last 30 Days (Month)</th>
            <th>Last 365 Days (Year)</th>
        </tr>
        <tr>
            <td><strong>Total System Page Visits</strong></td>
            <td><?= (int)$trafficStats['visits_hour'] ?></td>
            <td><?= (int)$trafficStats['visits_day'] ?></td>
            <td><?= (int)$trafficStats['visits_week'] ?></td>
            <td><?= (int)$trafficStats['visits_month'] ?></td>
            <td><strong><?= (int)$trafficStats['visits_year'] ?></strong></td>
        </tr>
        <tr>
            <td><strong>Successful User Logins</strong></td>
            <td><?= (int)$trafficStats['logins_hour'] ?></td>
            <td><?= (int)$trafficStats['logins_day'] ?></td>
            <td><?= (int)$trafficStats['logins_week'] ?></td>
            <td><?= (int)$trafficStats['logins_month'] ?></td>
            <td><strong><?= (int)$trafficStats['logins_year'] ?></strong></td>
        </tr>
        <tr>
            <td><strong>New Account Registrations</strong></td>
            <td><?= (int)$trafficStats['reg_hour'] ?></td>
            <td><?= (int)$trafficStats['reg_day'] ?></td>
            <td><?= (int)$trafficStats['reg_week'] ?></td>
            <td><?= (int)$trafficStats['reg_month'] ?></td>
            <td><strong><?= (int)$trafficStats['reg_year'] ?></strong></td>
        </tr>
    </table>

    <!-- SECTION 2: REGISTERED USERS SUMMARY -->
    <h3 style="margin-top:26px;">2. Registered Users &amp; Account Moderation Summary</h3>
    <div class="kpi-grid">
        <div class="kpi-card">
            <h4>Total Registered Accounts</h4>
            <p><?= (int)$userStats['total_users'] ?></p>
            <small><?= (int)$userStats['total_tourists'] ?> Tourists &bull; <?= (int)$userStats['total_admins'] ?> Admins</small>
        </div>
        <div class="kpi-card">
            <h4>Active Users</h4>
            <p><?= (int)$userStats['active_users'] ?></p>
            <small>In good standing</small>
        </div>
        <div class="kpi-card kpi-warn">
            <h4>Flagged Accounts</h4>
            <p><?= (int)$userStats['flagged_users'] ?></p>
            <small>Under Admin review</small>
        </div>
        <div class="kpi-card kpi-danger">
            <h4>Suspended Accounts</h4>
            <p><?= (int)$userStats['suspended_users'] ?></p>
            <small>Login access blocked</small>
        </div>
    </div>

    <!-- SECTION 3: BOOKING & FINANCIAL TOTALS & AVERAGES -->
    <h3 style="margin-top:26px;">3. Financial Totals &amp; Booking Averages (<?= htmlspecialchars($activeCurrency) ?>)</h3>
    <div class="kpi-grid">
        <div class="kpi-card">
            <h4>Total Realized Revenue</h4>
            <p><?= formatMoney((float)$kpi['realized_revenue_usd']) ?></p>
            <small>From <?= (int)$kpi['confirmed_count'] + (int)$kpi['concluded_count'] ?> Confirmed &amp; Concluded safaris</small>
        </div>
        <div class="kpi-card">
            <h4>Average Confirmed Booking</h4>
            <p><?= formatMoney((float)$kpi['avg_confirmed_value_usd']) ?></p>
            <small>Avg Group Size: <?= number_format((float)$kpi['avg_group_size'], 1) ?> guests</small>
        </div>
        <div class="kpi-card kpi-warn">
            <h4>Pending Revenue Pipeline</h4>
            <p><?= formatMoney((float)$kpi['pending_revenue_usd']) ?></p>
            <small><?= (int)$kpi['pending_count'] ?> Pending bookings</small>
        </div>
        <div class="kpi-card kpi-danger">
            <h4>Cancelled Bookings</h4>
            <p><?= (int)$kpi['cancelled_count'] ?></p>
            <small>Overall Avg Order: <?= formatMoney((float)$kpi['avg_all_orders_value']) ?></small>
        </div>
    </div>

    <!-- VISUAL CHARTS (Hidden on Paper Printout) -->
    <div class="grid no-print" style="margin: 22px 0;">
        <div class="card">
            <h4 style="margin-top:0; color:var(--accent-neon);">📊 Booking Status Breakdown</h4>
            <canvas id="bookingChart" height="190"></canvas>
        </div>
        <div class="card">
            <h4 style="margin-top:0; color:var(--accent-neon);">📈 System Traffic &amp; Logins Overview</h4>
            <canvas id="trafficChart" height="190"></canvas>
        </div>
    </div>

    <!-- FILTER BAR (Hidden on Paper Printout) -->
    <div class="filter-bar no-print">
        <strong>Filter Detailed Bookings:</strong>
        <?php foreach ($allowedStatuses as $st): ?>
            <a href="?status=<?= $st ?>" class="btn <?= $statusFilter === $st ? '' : 'btn-edit' ?>">
                <?= $st === 'ALL' ? 'All Bookings' : $st ?>
            </a>
        <?php endforeach; ?>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Tourist</th>
            <th>Package &amp; Destination</th>
            <th>Travel Date</th>
            <th>Guests</th>
            <th>Total (<?= htmlspecialchars($activeCurrency) ?>)</th>
            <th>Status</th>
            <th>Reason / Note</th>
        </tr>
        <?php if (empty($rows)): ?>
            <tr><td colspan="8" style="text-align:center;">No bookings found under this filter.</td></tr>
        <?php else: ?>
            <?php foreach ($rows as $r): ?>
            <tr>
                <td>#<?= (int)$r['booking_id'] ?></td>
                <td><?= htmlspecialchars($r['full_name']) ?><br><small style="color:var(--text-muted);"><?= htmlspecialchars($r['email']) ?></small></td>
                <td><strong><?= htmlspecialchars($r['title']) ?></strong><br><small style="color:var(--text-muted);"><?= htmlspecialchars($r['destination']) ?></small></td>
                <td><?= htmlspecialchars($r['travel_date']) ?></td>
                <td><?= (int)$r['num_guests'] ?></td>
                <td><?= formatMoney((float)$r['total_cost']) ?></td>
                <td><span class="badge badge-<?= htmlspecialchars($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                <td><?= htmlspecialchars($r['status_reason'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<script src="https://jsdelivr.net"></script>
<script>
if (typeof Chart !== 'undefined') {
    new Chart(document.getElementById('bookingChart'), {
        type: 'doughnut',
        data: {
            labels: ['Confirmed', 'Concluded', 'Pending', 'Cancelled'],
            datasets: [{
                data: [<?= (int)$kpi['confirmed_count'] ?>, <?= (int)$kpi['concluded_count'] ?>, <?= (int)$kpi['pending_count'] ?>, <?= (int)$kpi['cancelled_count'] ?>],
                backgroundColor: ['#00f59b', '#38bdf8', '#ffb703', '#ff4d6d'],
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { labels: { color: '#f0fdf4' } } } }
    });

    new Chart(document.getElementById('trafficChart'), {
        type: 'bar',
        data: {
            labels: ['Last 1 Hr', 'Today', 'This Week', 'This Month', 'This Year'],
            datasets: [
                {
                    label: 'Page Visits',
                    data: [<?= (int)$trafficStats['visits_hour'] ?>, <?= (int)$trafficStats['visits_day'] ?>, <?= (int)$trafficStats['visits_week'] ?>, <?= (int)$trafficStats['visits_month'] ?>, <?= (int)$trafficStats['visits_year'] ?>],
                    backgroundColor: '#00f59b'
                },
                {
                    label: 'Logins',
                    data: [<?= (int)$trafficStats['logins_hour'] ?>, <?= (int)$trafficStats['logins_day'] ?>, <?= (int)$trafficStats['logins_week'] ?>, <?= (int)$trafficStats['logins_month'] ?>, <?= (int)$trafficStats['logins_year'] ?>],
                    backgroundColor: '#ffb703'
                }
            ]
        },
        options: {
            scales: {
                x: { ticks: { color: '#9eb3aa' } },
                y: { ticks: { color: '#9eb3aa' }, beginAtZero: true }
            },
            plugins: { legend: { labels: { color: '#f0fdf4' } } }
        }
    });
}
</script>
<?php include 'footer.php'; ?>
</body>
</html>
