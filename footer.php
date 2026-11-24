<footer class="site-footer">
    <div class="footer-content">
        <p>&copy; <?= date('Y') ?> <strong>Pearl Trails Tours &amp; Travel Ug Ltd</strong>. All Rights Reserved.</p>
        <p class="footer-sub">
            Plot 14, Kampala-Jinja Highway, Nagojje, Mukono District, Uganda &bull;
            <a href="about.php">About Us &amp; Contacts</a> &bull;
            <a href="index.php?offers=1">Weekend &amp; Holiday Offers</a>
            <a href="privacy_policy.php">Privacy Policy</a>
        </p>
    </div>
</footer>

<?php
if (file_exists(__DIR__ . '/chat_widget.php')) {
    include_once __DIR__ . '/chat_widget.php';
}
?>

<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#00f59b">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Pearl Trails">
<link rel="apple-touch-icon" href="https://flaticon.com">

<div id="offlineStatusBanner" style="display:none; position:fixed; top:0; left:0; width:100%; background:#ffb703; color:#0b1310; text-align:center; font-weight:bold; font-size:13px; padding:5px; z-index:9999;">
    📡 Offline Park Mode Active — Viewing Cached Safari Maps, Packages &amp; E-Tickets
</div>

<!-- Floating One-Click App Installer for Laptops, Tablets & Smartphones -->
<button type="button" id="installPwaAppBtn" class="no-print" style="display:none; position:fixed; bottom:20px; right:20px; background:#ffb703; color:#0b1310; border:none; border-radius:30px; padding:12px 18px; font-weight:800; font-size:13.5px; cursor:pointer; box-shadow:0 6px 20px rgba(255,183,3,0.45); z-index:2300;">
    📲 Install Pearl Trails App
</button>

<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').catch(function() {});
}

let deferredInstallPrompt = null;
const installBtn = document.getElementById('installPwaAppBtn');

window.addEventListener('beforeinstallprompt', function(e) {
    e.preventDefault();
    deferredInstallPrompt = e;
    if (installBtn) {
        installBtn.style.display = 'block';
    }
});

if (installBtn) {
    installBtn.addEventListener('click', async function() {
        if (!deferredInstallPrompt) {
            alert('To install on iPhone/iPad: tap the Share button in Safari and select "Add to Home Screen".');
            return;
        }
        deferredInstallPrompt.prompt();
        const choice = await deferredInstallPrompt.userChoice;
        if (choice.outcome === 'accepted') {
            installBtn.style.display = 'none';
        }
        deferredInstallPrompt = null;
    });
}

window.addEventListener('appinstalled', function() {
    if (installBtn) installBtn.style.display = 'none';
});

function updateNetworkBanner() {
    var b = document.getElementById('offlineStatusBanner');
    if (b) b.style.display = navigator.onLine ? 'none' : 'block';
}
window.addEventListener('online', updateNetworkBanner);
window.addEventListener('offline', updateNetworkBanner);
updateNetworkBanner();
</script>
</body>
</html>
