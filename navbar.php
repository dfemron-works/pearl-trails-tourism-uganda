<?php
require_once 'currency_helper.php';
if (file_exists(__DIR__ . '/weather_geo_helper.php')) {
    require_once __DIR__ . '/weather_geo_helper.php';
}
if (!function_exists('fetchLiveLocationWeather')) {
    function fetchLiveLocationWeather(string $placeQuery): array {
        $name = trim($placeQuery) !== '' ? trim($placeQuery) : 'Kampala (Uganda)';
        return [
            'location' => $name,
            'temp_c' => 26.5,
            'wind_kmh' => 11.5,
            'condition' => '⛅ Partly Cloudy',
            'humidity' => 68,
            'lat' => 0.4167,
            'lon' => 32.8833,
            'elevation_m' => 1180,
            'forecast_3day' => []
        ];
    }
}

$currentPage = basename($_SERVER['PHP_SELF']);
$isOffersView = ($currentPage === 'index.php' && isset($_GET['offers']) && $_GET['offers'] === '1');
?>
<style>
/* Top Brand & Realistic Safari Land Cruiser Animation Bar */
.top-safari-header-strip {
    width: 100%;
    box-sizing: border-box;
    background: linear-gradient(90deg, #0b1310 0%, #13201b 45%, #192b24 100%);
    border-bottom: 1px solid var(--border-color);
    padding: 8px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    overflow: hidden;
}

.brand-title-box {
    flex-shrink: 0;
    z-index: 5;
    background: rgba(11, 19, 16, 0.92);
    padding: 6px 14px 6px 0;
    border-right: 2px solid var(--accent-neon);
}

.brand-title-box strong {
    font-size: 20px;
    color: var(--accent-neon);
    letter-spacing: 0.4px;
    display: block;
    line-height: 1.2;
}

.brand-title-box small {
    font-size: 11px;
    color: #ffb703;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.savanna-runway {
    flex: 1;
    height: 76px;
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    background: linear-gradient(180deg, rgba(255, 183, 3, 0.08) 0%, rgba(11, 19, 16, 0.6) 76%, #2b3a2e 76%, #192b24 100%);
    border: 1px solid rgba(0, 245, 155, 0.22);
}

.savanna-horizon {
    position: absolute;
    bottom: 14px;
    width: 200%;
    display: flex;
    justify-content: space-around;
    font-size: 18px;
    opacity: 0.55;
    animation: savannaPassRight 18s linear infinite;
    pointer-events: none;
}

.cruiser-hero-rig {
    position: absolute;
    bottom: 4px;
    right: -320px;
    animation: driveRightToLeft 13s linear infinite, cruiserBounce 0.42s ease-in-out infinite alternate;
    will-change: right, transform;
}

.hero-wheel {
    transform-origin: center;
    transform-box: fill-box;
    animation: spinReverse 0.55s linear infinite;
}

.camera-flash {
    animation: flashPulse 2.8s infinite;
}

@keyframes driveRightToLeft {
    0%   { right: -320px; }
    100% { right: 102%; }
}

@keyframes cruiserBounce {
    0%   { transform: translateY(0px); }
    100% { transform: translateY(-2.2px); }
}

@keyframes spinReverse {
    0%   { transform: rotate(360deg); }
    100% { transform: rotate(0deg); }
}

@keyframes savannaPassRight {
    0%   { transform: translateX(-50%); }
    100% { transform: translateX(0%); }
}

@keyframes flashPulse {
    0%, 88%, 100% { opacity: 0; }
    92% { opacity: 0.95; }
    96% { opacity: 0.2; }
}
</style>

<!-- TOP ROW: System Title on Left + Large Safari Land Cruiser Running Right-to-Left -->
<div class="top-safari-header-strip">
    <div class="brand-title-box">
        <strong>Pearl Trails Tours &amp; Travel Uganda Ltd</strong>
        <small>Explore the Pearl of Africa 🇺🇬</small>
    </div>

    <div class="savanna-runway" title="Pearl Trails Tours & Travel Uganda Ltd — Live Savanna Expedition">
        <div class="savanna-horizon">
            <span>🌳</span><span>🦒</span><span>🌾</span><span>🐘</span><span>🌴</span><span>🦓</span>
            <span>🌳</span><span>🦒</span><span>🌾</span><span>🐘</span><span>🌴</span><span>🦓</span>
        </div>

        <div class="cruiser-hero-rig">
            <svg width="305" height="68" viewBox="0 0 305 68" xmlns="http://w3.org">
                <defs>
                    <linearGradient id="bodyGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#e9d8a6"/>
                        <stop offset="55%" stop-color="#ca9e67"/>
                        <stop offset="100%" stop-color="#8c6239"/>
                    </linearGradient>
                    <linearGradient id="glassGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.55"/>
                        <stop offset="100%" stop-color="#0b1310" stop-opacity="0.95"/>
                    </linearGradient>
                </defs>

                <circle cx="286" cy="52" r="5" fill="#d4a373" opacity="0.35"/>
                <circle cx="296" cy="49" r="4" fill="#d4a373" opacity="0.25"/>
                <circle cx="302" cy="54" r="3" fill="#d4a373" opacity="0.2"/>

                <rect x="108" y="4" width="136" height="4.5" rx="2" fill="#f4a261" stroke="#5c4033" stroke-width="1"/>
                <line x1="118" y1="8.5" x2="118" y2="19" stroke="#cbd5e1" stroke-width="2"/>
                <line x1="234" y1="8.5" x2="234" y2="19" stroke="#cbd5e1" stroke-width="2"/>

                <circle cx="142" cy="13.5" r="4.5" fill="#f4a261"/>
                <path d="M136 10 Q142 6 148 10" fill="#2b2d42"/>
                <path d="M134 19 Q142 16 150 19" fill="#00f59b"/>
                <rect x="128" y="12" width="10" height="5.5" rx="1" fill="#111827"/>
                <rect x="119" y="13" width="9" height="3.5" rx="0.8" fill="#374151"/>
                <polygon class="camera-flash" points="119,14.5 103,8 103,21" fill="#fffbeb"/>

                <circle cx="196" cy="13.5" r="4.5" fill="#e9c46a"/>
                <path d="M190 10 Q196 6 202 10" fill="#8c6239"/>
                <path d="M188 19 Q196 16 204 19" fill="#38bdf8"/>
                <rect x="186" y="12.5" width="7" height="3.5" rx="1" fill="#1f2937"/>

                <rect x="268" y="23" width="9" height="24" rx="3" fill="#111827" stroke="#00f59b" stroke-width="1.2"/>

                <path d="M18 36 L10 36 L10 49 L22 49" fill="none" stroke="#cbd5e1" stroke-width="2.5"/>
                <path d="M84 19 L84 11 L89 11" fill="none" stroke="#111827" stroke-width="2.5"/>

                <path d="M22 50 L22 38 Q22 33 32 32 L78 29 L96 19 L264 19 L268 35 L268 50 Z" fill="url(#bodyGrad)" stroke="#00f59b" stroke-width="1.5"/>

                <polygon points="99,21 82,30 108,30 108,21" fill="url(#glassGrad)" stroke="#1f2937" stroke-width="1"/>
                <circle cx="96" cy="26" r="3.6" fill="#8d5524"/>

                <rect x="114" y="21" width="44" height="10" rx="2" fill="url(#glassGrad)" stroke="#1f2937" stroke-width="1"/>
                <rect x="163" y="21" width="44" height="10" rx="2" fill="url(#glassGrad)" stroke="#1f2937" stroke-width="1"/>
                <rect x="212" y="21" width="46" height="10" rx="2" fill="url(#glassGrad)" stroke="#1f2937" stroke-width="1"/>

                <rect x="44" y="34" width="216" height="11" rx="2.5" fill="#0b1310" opacity="0.9"/>
                <text x="152" y="41.8" fill="#00f59b" font-family="Arial, sans-serif" font-size="8.2" font-weight="bold" text-anchor="middle" letter-spacing="0.4">
                    PEARL TRAILS TOURS &amp; TRAVEL UGANDA LTD
                </text>

                <polygon points="20,38 0,32 0,48 20,44" fill="#ffb703" opacity="0.45"/>
                <circle cx="22" cy="41" r="3" fill="#fef08a"/>

                <g class="hero-wheel">
                    <circle cx="64" cy="52" r="11.5" fill="#0b1310" stroke="#00f59b" stroke-width="2.4"/>
                    <circle cx="64" cy="52" r="5" fill="#cbd5e1"/>
                    <line x1="64" y1="41" x2="64" y2="63" stroke="#64748b" stroke-width="1.5"/>
                    <line x1="53" y1="52" x2="75" y2="52" stroke="#64748b" stroke-width="1.5"/>
                </g>

                <g class="hero-wheel">
                    <circle cx="226" cy="52" r="11.5" fill="#0b1310" stroke="#00f59b" stroke-width="2.4"/>
                    <circle cx="226" cy="52" r="5" fill="#cbd5e1"/>
                    <line x1="226" y1="41" x2="226" y2="63" stroke="#64748b" stroke-width="1.5"/>
                    <line x1="215" y1="52" x2="237" y2="52" stroke="#64748b" stroke-width="1.5"/>
                </g>
            </svg>
        </div>
    </div>
</div>
<!-- NAVIGATION MENU BAR -->
<header style="border-top: none; padding: 10px 24px;">
    <nav style="width:100%; display:flex; flex-direction:column; gap:8px;">
        <!-- ROW 1: All Navigation Tabs + Currency Exchanger + Alerts + Settings in ONE Single Line -->
        <div style="width:100%; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px;">
            <div style="display:flex; flex-wrap:wrap; align-items:center; gap:4px;">
                <a href="index.php" class="<?= ($currentPage === 'index.php' && !$isOffersView) ? 'active' : '' ?>">Packages</a>
                <a href="index.php?offers=1" class="nav-offer-btn <?= $isOffersView ? 'active' : '' ?>">🔥 Offers</a>
                <a href="safari_map.php" class="<?= $currentPage === 'safari_map.php' ? 'active' : '' ?>">🗺️ Live Safari Map</a>
                <a href="gallery.php" class="<?= $currentPage === 'gallery.php' ? 'active' : '' ?>">Gallery</a>
                <a href="reviews.php" class="<?= $currentPage === 'reviews.php' ? 'active' : '' ?>">Feedback</a>
                <a href="about.php" class="<?= $currentPage === 'about.php' ? 'active' : '' ?>">About Us</a>
                <a href="weather_geography.php" class="<?= $currentPage === 'weather_geography.php' ? 'active' : '' ?>">🌦️ Weather &amp; Geo</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin_dashboard.php" class="<?= in_array($currentPage, ['admin_dashboard.php', 'edit_package.php']) ? 'active' : '' ?>">Records Manager</a>
                        <a href="manage_users.php" class="<?= $currentPage === 'manage_users.php' ? 'active' : '' ?>">Manage Users</a>
                        <a href="inventory.php" class="<?= $currentPage === 'inventory.php' ? 'active' : '' ?>">📦 Inventory</a>
                        <a href="finances.php" class="<?= $currentPage === 'finances.php' ? 'active' : '' ?>">📊 Finances</a>
                        <a href="reports.php" class="<?= $currentPage === 'reports.php' ? 'active' : '' ?>">Reports</a>
                        <a href="weather_report.php" class="<?= $currentPage === 'weather_report.php' ? 'active' : '' ?>">⛈️ Weather Report</a>
                    <?php else: ?>
                        <a href="my_bookings.php" class="<?= $currentPage === 'my_bookings.php' ? 'active' : '' ?>">My Bookings</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['full_name']) ?>)</a>
                <?php else: ?>
                    <a href="login.php" class="<?= $currentPage === 'login.php' ? 'active' : '' ?>">Login</a>
                    <a href="register.php" class="<?= $currentPage === 'register.php' ? 'active' : '' ?>">Register</a>
                <?php endif; ?>
            </div>

            <!-- Right Controls on Same Line: USD Exchanger, Alerts & Settings -->
            <div style="display:flex; align-items:center; gap:6px; margin-left:auto;">
                <form method="GET" style="margin:0; display:inline-block;">
                    <?php foreach ($_GET as $k => $v): ?>
                        <?php if ($k !== 'set_currency'): ?>
                            <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <select name="set_currency" onchange="this.form.submit()" style="margin:0; padding:6px 10px; width:auto; border-color:var(--accent-neon); font-weight:bold;" title="Switch Currency">
                        <?php foreach ($SUPPORTED_CURRENCIES as $code => $info): ?>
                            <option value="<?= $code ?>" <?= $activeCurrency === $code ? 'selected' : '' ?>>
                                <?= $code ?> (<?= trim($info['symbol']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <button type="button" id="adminAlertBell" onclick="window.toggleAdminAlerts()" style="padding:6px 11px; background:#192b24; color:var(--accent-neon); border-color:var(--accent-neon);">
                        🔔 Alerts (<span id="alertBadgeCount">0</span>)
                    </button>
                <?php endif; ?>

                <button type="button" id="openSettingsBtn" style="padding:6px 11px;">⚙️ Settings</button>
            </div>
        </div>

        <!-- ROW 2 & 3 (Logged-In Only): Full End-to-End Uganda Geographical Banner + Full End-to-End Weather Ticker -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php
                $navHubs = ['Nagojje', 'Kampala', 'Bwindi', 'Murchison', 'Queen Elizabeth', 'Jinja', 'Kidepo', 'Entebbe', 'Nairobi', 'London', 'New York', 'Dubai'];
                $navTickerParts = [];
                foreach ($navHubs as $nh) {
                    $nw = fetchLiveLocationWeather($nh);
                    $navTickerParts[] = "📍 {$nw['location']}: {$nw['temp_c']}°C ({$nw['condition']}, Wind {$nw['wind_kmh']} km/h)";
                }
                $navTickerString = implode('   &nbsp;&bull;&nbsp;   ', $navTickerParts);
            ?>
            <style>
                .uganda-nav-geo-banner {
                    width: 100%;
                    box-sizing: border-box;
                    background: #0b1310;
                    border: 1px solid var(--border-color);
                    border-left: 4px solid #ffb703;
                    border-radius: 5px;
                    padding: 8px 14px;
                    line-height: 1.4;
                }
                .nav-full-wx-banner {
                    width: 100%;
                    box-sizing: border-box;
                    background: #0b1310;
                    border: 1.5px solid var(--accent-neon);
                    border-radius: 5px;
                    padding: 8px 14px;
                    overflow: hidden;
                    white-space: nowrap;
                    font-size: 15.5px;
                    font-weight: 500;
                    letter-spacing: 0.3px;
                    color: #f0fdf4;
                    box-shadow: inset 0 0 12px rgba(0, 245, 155, 0.14);
                }
                .nav-full-wx-track {
                    display: inline-block;
                    padding-left: 100%;
                    animation: navFullWxScroll 95s linear infinite;
                }
                .nav-full-wx-banner:hover .nav-full-wx-track {
                    animation-play-state: paused;
                    cursor: pointer;
                }
                @keyframes navFullWxScroll {
                    0%   { transform: translateX(0%); }
                    100% { transform: translateX(-100%); }
                }
            </style>

            <!-- End-to-End Uganda Geographical Banner (Larger, High-Readability Text) -->
            <div class="uganda-nav-geo-banner no-print" style="padding: 10px 16px; line-height: 1.55;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:5px;">
                    <strong style="color:var(--accent-neon); font-size:16.5px;">Republic of Uganda (The Pearl of Africa)</strong>
                    <span style="color:#ffb703; font-size:14px; font-weight:600;">GPS: 1.3733° N, 32.2903° E &bull; Elevation: 1,100m average (Lake Albert 621m to Margherita Peak 5,109m)</span>
                </div>
                <div style="font-size:15px; color:#f0fdf4; margin-bottom:4px;">
                    <strong style="color:var(--accent-neon);">Geography:</strong> Landlocked East African nation bordered by Kenya, Tanzania, Rwanda, DR Congo, and South Sudan. Home to Lake Victoria, the Source of the River Nile, Rwenzori Mountains, and 10 National Parks.
                </div>
                <div style="font-size:15px; color:#f0fdf4;">
                    <strong style="color:var(--accent-neon);">Climate &amp; Seasons:</strong> Equatorial tropical climate moderated by altitude; two dry seasons (Dec–Feb, Jun–Aug) and two wet seasons (Mar–May, Sep–Nov).
                </div>
            </div>

            <!-- End-to-End Running Live Weather Updates Banner -->
            <div class="nav-full-wx-banner no-print" title="Hover cursor here anytime to pause and read details">
                <div class="nav-full-wx-track">
                    <strong style="color:var(--accent-neon); font-size:16px;">🌦️ LIVE UGANDA &amp; WORLD WEATHER FORECAST:</strong> &nbsp;&nbsp; <?= $navTickerString ?>
                </div>
            </div>
        <?php endif; ?>
    </nav>
</header>
<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div id="adminAlertDrawer" style="display:none; position:fixed; top:128px; right:20px; width:380px; max-width:94vw; max-height:75vh; overflow-y:auto; background:#13201b; border:2px solid var(--accent-neon); border-radius:12px; padding:16px; z-index:2500; box-shadow:0 12px 35px rgba(0,0,0,0.85);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <strong style="color:var(--accent-neon);">🔔 Real-Time System Alerts</strong>
            <div>
                <button type="button" onclick="window.markAllAlertsRead()" style="padding:3px 8px; font-size:11px;">Mark Read</button>
                <button type="button" onclick="window.toggleAdminAlerts()" style="padding:3px 8px; font-size:11px; background:var(--danger-neon); border-color:var(--danger-neon); color:#fff;">✕</button>
            </div>
        </div>
        <div id="adminAlertsList" style="display:flex; flex-direction:column; gap:8px; font-size:13px;">
            <p style="color:var(--text-muted);">Loading live alerts...</p>
        </div>
    </div>

    <div id="liveAlertToast" style="display:none; position:fixed; bottom:20px; right:20px; width:340px; max-width:90vw; background:#13201b; border:2px solid #ffb703; border-radius:10px; padding:14px; z-index:2600; box-shadow:0 8px 25px rgba(0,0,0,0.8);"></div>

    <script>
    var lastSeenAlertId = parseInt(localStorage.getItem('pt_last_alert_id') || '0', 10);

    window.toggleAdminAlerts = function() {
        var drawer = document.getElementById('adminAlertDrawer');
        if (!drawer) return;
        drawer.style.display = (drawer.style.display === 'block') ? 'none' : 'block';
    };

    window.markAllAlertsRead = function() {
        fetch('alerts_helper.php?alerts_api=mark_read').then(function() {
            var badge = document.getElementById('alertBadgeCount');
            if (badge) badge.textContent = '0';
            window.pollAdminAlerts();
        });
    };

    window.showToastPopup = function(alertObj) {
        var toast = document.getElementById('liveAlertToast');
        if (!toast) return;
        var borderCol = (alertObj.severity === 'DANGER') ? '#ff4d6d' : '#00f59b';
        toast.style.borderColor = borderCol;
        toast.innerHTML = '';

        var titleEl = document.createElement('strong');
        titleEl.style.cssText = 'color:' + borderCol + '; display:block; margin-bottom:4px;';
        titleEl.textContent = alertObj.title;

        var msgEl = document.createElement('span');
        msgEl.style.cssText = 'font-size:13px; color:#f0fdf4;';
        msgEl.textContent = alertObj.message;

        toast.appendChild(titleEl);
        toast.appendChild(msgEl);
        toast.style.display = 'block';
        setTimeout(function() { toast.style.display = 'none'; }, 6500);
    };

    window.pollAdminAlerts = function() {
        fetch('alerts_helper.php?alerts_api=poll')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                var badge = document.getElementById('alertBadgeCount');
                var list = document.getElementById('adminAlertsList');
                if (badge) badge.textContent = data.unread_count;

                if (data.alerts && data.alerts.length > 0) {
                    var newest = data.alerts[0];
                    if (parseInt(newest.alert_id, 10) > lastSeenAlertId) {
                        if (lastSeenAlertId !== 0) {
                            window.showToastPopup(newest);
                        }
                        lastSeenAlertId = parseInt(newest.alert_id, 10);
                        localStorage.setItem('pt_last_alert_id', String(lastSeenAlertId));
                    }

                    list.innerHTML = '';
                    data.alerts.forEach(function(item) {
                        var card = document.createElement('div');
                        var col = (item.severity === 'DANGER') ? '#ff4d6d' : ((item.severity === 'WARNING') ? '#ffb703' : '#00f59b');
                        card.style.cssText = 'padding:10px; border-radius:6px; background:#0b1310; border-left:4px solid ' + col + '; opacity:' + (item.is_read == 1 ? '0.65' : '1');

                        var t = document.createElement('strong');
                        t.style.color = col;
                        t.textContent = item.title;

                        var m = document.createElement('div');
                        m.style.margin = '4px 0';
                        m.textContent = item.message;

                        var meta = document.createElement('small');
                        meta.style.color = '#9eb3aa';
                        meta.textContent = item.source_channel + ' • ' + item.created_at;

                        card.appendChild(t);
                        card.appendChild(m);
                        card.appendChild(meta);
                        list.appendChild(card);
                    });
                } else {
                    list.innerHTML = '<p style="color:var(--text-muted);">No alerts recorded yet.</p>';
                }
            })
            .catch(function() {});
    };

    document.addEventListener('DOMContentLoaded', function() {
        window.pollAdminAlerts();
        setInterval(window.pollAdminAlerts, 5000);
    });
    </script>
<?php endif; ?>

<script>
document.addEventListener('click', function(e) {
    var btn = e.target.closest('#openSettingsBtn');
    if (btn) {
        var modal = document.getElementById('settingsModal');
        if (modal) {
            modal.style.display = (modal.style.display === 'block') ? 'none' : 'block';
        }
    }
});
</script>
<?php include 'settings_widget.php'; ?>
