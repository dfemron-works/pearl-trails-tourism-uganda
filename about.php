<?php
require 'db.php';
$inquiryFeedback = '';

// Trigger alert when a visitor clicks any social media handle
if (isset($_GET['social_ping'])) {
    $platform = htmlspecialchars(trim($_GET['social_ping']));
    $sender = $_SESSION['full_name'] ?? 'Guest Visitor';
    triggerAdminAlert(
        $pdo,
        'SOCIAL_MSG',
        'WARNING',
        "📱 Outbound Social Message ($platform)",
        "$sender initiated a message via our official $platform handle.",
        $platform
    );
    exit;
}

// Handle User Query / Inquiry Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_inquiry'])) {
    $senderName = trim($_POST['sender_name']);
    $senderEmail = trim($_POST['sender_email']);
    $channel = trim($_POST['channel']);
    $queryText = trim($_POST['query_text']);

    if ($senderName !== '' && $queryText !== '') {
        $isAbusive = checkAndAlertAbusiveLanguage($pdo, $queryText, "Inquiry via $channel", $_SESSION['user_id'] ?? null);

        $alertType = ($channel === 'Web Inquiry Form') ? 'USER_INQUIRY' : 'SOCIAL_MSG';
        $severity = $isAbusive ? 'DANGER' : 'INFO';
        triggerAdminAlert(
            $pdo,
            $alertType,
            $severity,
            "✉️ New Query via $channel",
            "From $senderName ($senderEmail): $queryText",
            $channel
        );

        $inquiryFeedback = $isAbusive
            ? "Warning: Your message was logged, but flagged for abusive or violent language."
            : "Thank you, $senderName! Your inquiry has been sent to our team in real time.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us & Contacts - Pearl Trails Uganda</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .info-list { list-style: none; padding: 0; margin: 12px 0 0; }
        .info-list li { padding: 10px 0; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
        .info-list li:last-child { border-bottom: none; }
        .info-label { color: var(--text-muted); font-weight: 600; }
        .info-val { color: var(--text-light); font-weight: 600; }
        .social-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 12px; margin-top: 14px; }
        .social-card { background: var(--bg-dark); border: 1px solid var(--border-color); border-radius: 8px; padding: 12px 14px; text-decoration: none; color: var(--text-light); display: flex; flex-direction: column; transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease; }
        .social-card:hover { transform: translateY(-3px); border-color: var(--accent-neon); box-shadow: 0 6px 18px rgba(0, 245, 155, 0.18); }
        .social-card strong { color: var(--accent-neon); font-size: 14px; }
        .social-card span { color: var(--text-muted); font-size: 13px; margin-top: 3px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2> About Pearl Trails Tours &amp; Travel Uganda Ltd</p></h2>
    <p style="color: var(--text-muted); max-width: 820px; text-align: justify; line-height: 1.6;">
        <strong>Pearl Trails Tours &amp; Travel Uganda Ltd</strong> is a licensed Ugandan eco-tourism and safari operator dedicated to delivering unforgettable Gorilla trekking, Wildlife game drives, Fresh water lakes visiting, Salt water lakes visiting, Skying, Gaming, Bird viewing, Crater lakes visits, Hotsprings visits, Beautiful Water falls, Source of the Nile, River trekking, adventure, Mountain Climbing, Game park viewing, cultural heritage tours, Guiding and adventure expeditions across the Pearl of Africa.
        We also do Tour license clearing, Airport picking and Dropping, Cab hire and Rental services, Tour Guiding services, Hotel Bookings and Visa processing.
    </p>


    <div class="grid" style="margin-top: 24px;">
        <div class="card">
            <h3 style="color: var(--accent-neon); margin-top: 0;">📍 Head Office & Location Address</h3>
            <ul class="info-list">
                <li><span class="info-label">Physical Street Address:</span> <span class="info-val">Plot 14, Kampala–Jinja Highway</span></li>
                <li><span class="info-label">Country:</span> <span class="info-val">Uganda 🇺🇬</span></li>
                <li><span class="info-label">District:</span> <span class="info-val">Mukono District</span></li>
                <li><span class="info-label">City / Municipality:</span> <span class="info-val">Mukono / Greater Kampala Metropolitan</span></li>
                <li><span class="info-label">Sub-County:</span> <span class="info-val">Nagojje Sub-County</span></li>
                <li><span class="info-label">Parish:</span> <span class="info-val">Nagojje Parish</span></li>
                <li><span class="info-label">Village:</span> <span class="info-val">Nagojje Central Village</span></li>
            </ul>
        </div>

        <div class="card">
            <h3 style="color: var(--accent-neon); margin-top: 0;">📞 Direct Contacts & Customer Desk</h3>
            <ul class="info-list">
                <li><span class="info-label">Office Telephone Line:</span> <span class="info-val">+256 778100580</span></li>
                <li><span class="info-label">24/7 Safari Mobile Line:</span> <span class="info-val">+256 785884677</span></li>
                <li>
                    <span class="info-label">WhatsApp Direct Chat:</span>
                    <a href="https://wa.me" target="_blank" onclick="fetch('about.php?social_ping=WhatsApp')" style="color: var(--accent-neon); font-weight:bold; text-decoration:none;">
                        +256 785884677 (Chat Now ↗)
                    </a>
                </li>
                <li><span class="info-label">General Inquiries Email:</span> <span class="info-val">info@pearltrails.ug</span></li>
                <li><span class="info-label">Reservations & Bookings:</span> <span class="info-val">bookings@pearltrails.ug</span></li>
                <li><span class="info-label">Operating Hours:</span> <span class="info-val">Mon – Sun: 7:00 AM – 9:00 PM EAT</span></li>
            </ul>
        </div>
    </div>

    <div class="card" style="margin-top: 24px;">
        <h3 style="color: var(--accent-neon); margin-top: 0;">✉️ Send Us a Direct Query or Social Handle Message</h3>
        <?php if ($inquiryFeedback): ?>
            <div class="alert <?= strpos($inquiryFeedback, 'Warning') === 0 ? 'error' : '' ?>"><?= htmlspecialchars($inquiryFeedback) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-top:0;">
                <div><label>Your Name *</label><input type="text" name="sender_name" value="<?= htmlspecialchars($_SESSION['full_name'] ?? '') ?>" required></div>
                <div><label>Email or Phone *</label><input type="text" name="sender_email" required></div>
                <div>
                    <label>Message Channel *</label>
                    <select name="channel">
                        <option value="Web Inquiry Form">Web Inquiry Form</option>
                        <option value="WhatsApp (+256 785884677)">WhatsApp</option>
                        <option value="Facebook (@PearlTrailsUganda)">Facebook</option>
                        <option value="Instagram (@pearltrails.ug)">Instagram</option>
                        <option value="X Handle (@PearlTrailsUG)">X (Twitter)</option>
                        <option value="YouTube (@PearlTrailsUgandaTV)">YouTube</option>
                        <option value="TikTok (@pearltrailsuganda)">TikTok</option>
                    </select>
                </div>
            </div>
            <label>Your Inquiry / Message *</label>
            <textarea name="query_text" rows="3" placeholder="Ask about gorilla permits, custom group rates, or holiday offers..." required></textarea>
            <button type="submit" name="send_inquiry">Send Message Now</button>
        </form>
    </div>

    <div class="card" style="margin-top: 24px;">
        <h3 style="color: var(--accent-neon); margin-top: 0;">🌐 Connect With Us on Social Media</h3>
        <div class="social-links">
            <a href="https://wa.me" target="_blank" onclick="fetch('about.php?social_ping=WhatsApp')" class="social-card">
                <strong>💬 WhatsApp Business</strong>
                <span>+256 785884677</span>
            </a>
            <a href="https://facebook.com" target="_blank" onclick="fetch('about.php?social_ping=Facebook')" class="social-card">
                <strong>📘 Facebook Page</strong>
                <span>@PearlTrailsUganda</span>
            </a>
            <a href="https://instagram.com" target="_blank" onclick="fetch('about.php?social_ping=Instagram')" class="social-card">
                <strong>📸 Instagram</strong>
                <span>@pearltrails.ug</span>
            </a>
            <a href="https://x.com" target="_blank" onclick="fetch('about.php?social_ping=X-Twitter')" class="social-card">
                <strong>𝕏 X (Twitter) Handle</strong>
                <span>@PearlTrailsUG</span>
            </a>
            <a href="https://youtube.com" target="_blank" onclick="fetch('about.php?social_ping=YouTube')" class="social-card">
                <strong>▶️ YouTube Channel</strong>
                <span>Pearl Trails Uganda TV</span>
            </a>
            <a href="https://tiktok.com" target="_blank" onclick="fetch('about.php?social_ping=TikTok')" class="social-card">
                <strong>🎵 TikTok Account</strong>
                <span>@pearltrailsuganda</span>
            </a>
        </div>
    </div>
</div>
</body>
</html>
