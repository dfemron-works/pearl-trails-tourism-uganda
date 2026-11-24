<?php
require_once 'db.php';
require_once 'currency_helper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Pearl Trails Tours &amp; Travel Uganda Ltd</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
        <h2>🔒 Privacy Policy &amp; Data Protection Notice</h2>
        <button type="button" onclick="window.print()" class="btn no-print">🖨️ Print Policy</button>
    </div>

    <p style="color:#ffb703; font-weight:600; margin-bottom:16px;">
        Effective Date: November 2026 &bull; Applicable to Web Portal, Android/iOS Mobile App &amp; REST APIs
    </p>

    <div class="card" style="background:#13201b; border:1px solid var(--border-color); border-radius:10px; padding:18px; margin-bottom:14px; line-height:1.65;">
        <h3 style="color:var(--accent-neon); margin-top:0;">1. Data Controller Information</h3>
        <p>
            <strong>Pearl Trails Tours &amp; Travel Uganda Ltd</strong> (Head Office: Plot 14, Kampala–Jinja Highway, Nagojje Central Village, Mukono District, Uganda) operates the Pearl Trails Web Portal and Mobile Companion Application. We process personal data in strict compliance with the <strong>Uganda Data Protection and Privacy Act (2019)</strong> and international data security standards.
        </p>

        <h3 style="color:var(--accent-neon);">2. Information We Collect</h3>
        <ul>
            <li><strong>Account &amp; Identity Details:</strong> Full name, email address, phone/WhatsApp number, and encrypted login credentials when you register an account.</li>
            <li><strong>Safari Booking &amp; Permit Records:</strong> Travel dates, group sizes, selected national parks, and Uganda Wildlife Authority (UWA) permit or visa clearing details.</li>
            <li><strong>Payment Verification Records:</strong> Transaction references and payment proofs for MTN Mobile Money, Airtel Money, Bank Transfers, or PayPal (we never store raw mobile money PINs or credit card numbers).</li>
            <li><strong>Location &amp; Meteorological Data:</strong> GPS coordinates used solely to render interactive route pins on the Live Safari Map and deliver localized weather forecasts.</li>
        </ul>

        <h3 style="color:var(--accent-neon);">3. How We Use Your Information</h3>
        <ul>
            <li>To confirm safari reservations, issue QR Boarding E-Tickets, and dispatch 4x4 safari vehicles and guides.</li>
            <li>To send real-time booking confirmations, payment receipts, and park weather advisories.</li>
            <li>To maintain financial accounting records and prevent fraudulent or abusive platform usage.</li>
        </ul>

        <h3 style="color:var(--accent-neon);">4. Data Security &amp; Retention</h3>
        <p>
            All passwords are protected using one-way cryptographic hashing, database queries use parameterized prepared statements, and external API traffic is secured via authenticated API keys and HTTPS encryption. We never sell or rent personal user data to third-party advertisers.
        </p>

        <h3 style="color:var(--accent-neon);">5. Your Rights &amp; Contact</h3>
        <p>
            You may inspect, update, or request deletion of your account records at any time by contacting our Data Protection Officer at <strong>info@pearltrails.ug</strong> or calling/WhatsApp <strong>+256 772 845 910</strong>.
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
