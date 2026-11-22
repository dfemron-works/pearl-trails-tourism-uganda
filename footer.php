<footer class="site-footer">
    <div class="footer-content">
        <p>&copy; <?= date('Y') ?> <strong>Pearl Trails Tours &amp; Travel Ug Ltd</strong>. All Rights Reserved.</p>
        <p class="footer-sub">
            Plot 14, Kampala–Jinja Highway, Nagojje, Mukono District, Uganda &bull; 
            <a href="about.php">About Us &amp; Contacts</a> &bull; 
            <a href="index.php?offers=1">Weekend &amp; Holiday Offers</a>
        </p>
    </div>
</footer>
<?php
if (file_exists(__DIR__ . '/chat_widget.php')) {
    include __DIR__ . '/chat_widget.php';
}
?>
<?php include_once 'chat_widget.php'; ?>