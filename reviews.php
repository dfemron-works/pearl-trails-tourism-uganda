<?php
require 'db.php';
$feedback = '';

// 1. Handle New Review Submission (Tourists Only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tourist') {
        header("Location: login.php");
        exit;
    }

    $package_id = (int)$_POST['package_id'];
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        $feedback = "Error: Please select a valid star rating between 1 and 5.";
    } elseif ($comment === '') {
        $feedback = "Error: Please write a short comment about your safari experience.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, package_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $package_id, $rating, $comment]);
        $feedback = "Thank you! Your " . $rating . "-star review has been published.";
    }
}

// 2. Fetch Tour Packages for Dropdown
$packages = $pdo->query("SELECT package_id, title, destination FROM tour_packages ORDER BY title ASC")->fetchAll();

// 3. Fetch Average Ratings Summary per Package
$averages = $pdo->query("
    SELECT t.title, ROUND(AVG(r.rating), 1) AS avg_rating, COUNT(r.review_id) AS total_reviews
    FROM tour_packages t
    LEFT JOIN reviews r ON t.package_id = r.package_id
    GROUP BY t.package_id
    ORDER BY avg_rating DESC
")->fetchAll();

// 4. Fetch All Tourist Reviews (JOIN across 3 tables: reviews, users, tour_packages)
$allReviews = $pdo->query("
    SELECT r.*, u.full_name, t.title AS package_title
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    JOIN tour_packages t ON r.package_id = t.package_id
    ORDER BY r.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tourist Reviews & Ratings - Pearl Trails Uganda</title>
    <link rel="stylesheet" href="style.css">
   <style>
    /* Ensure all Review / Feedback cards match the dark emerald theme with high-contrast readable text */
    .review-card, .card, .feedback-item {
        background: #13201b !important;
        color: #9a9f07 !important;
        border: 1px solid var(--border-color) !important;
        border-left: 4px solid var(--accent-neon) !important;
        border-radius: 10px;
        padding: 16px 18px;
        margin-bottom: 14px;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .review-card:hover, .card:hover, .feedback-item:hover {
        border-color: var(--accent-neon) !important;
        background: #37f7aa !important;
    }
    .review-card p, .card p, .feedback-item p,
    .review-card span, .card span, .feedback-item span {
        color: #0a0a9e !important;
    }
    .review-card small, .card small, .feedback-item small {
        color: #70a71d !important;
    }
    .review-card strong, .card strong, .feedback-item strong {
        color: var(--accent-neon) !important;
    }
</style>

<body>
<?php include 'navbar.php'; ?>
    <strong>Pearl Trails Uganda</strong>
    <nav>
        <a href="index.php">Packages</a>
        <a href="reviews.php">Feedback</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.php">Records Manager</a>
            <?php else: ?>
                <a href="my_bookings.php">My Bookings</a>
            <?php endif; ?>
            <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['full_name']) ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>

<div class="container">
    <h2>Safari Package Ratings & Tourist Reviews</h2>

    <?php if ($feedback): ?>
        <div class="alert <?= str_starts_with($feedback, 'Error') ? 'error' : '' ?>">
            <?= htmlspecialchars($feedback) ?>
        </div>
    <?php endif; ?>

    <!-- Average Rating Summary Table -->
    <h3>Package Rating Summary</h3>
    <table>
        <tr><th>Safari Package</th><th>Average Rating</th><th>Total Reviews</th></tr>
        <?php foreach ($averages as $avg): ?>
        <tr>
            <td><?= htmlspecialchars($avg['title']) ?></td>
            <td>
                <?php if ($avg['avg_rating']): ?>
                    <span class="stars"><?= str_repeat('★', (int)round($avg['avg_rating'])) ?></span>
                    (<?= $avg['avg_rating'] ?> / 5.0)
                <?php else: ?>
                    No ratings yet
                <?php endif; ?>
            </td>
            <td><?= $avg['total_reviews'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Tourist Review Submission Form -->
    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'tourist'): ?>
        <div class="card" style="margin-top: 25px;">
            <h3>Write a Safari Review</h3>
            <form method="POST">
                <label>Select Safari Package</label>
                <select name="package_id" required>
                    <?php foreach ($packages as $p): ?>
                        <option value="<?= $p['package_id'] ?>">
                            <?= htmlspecialchars($p['title']) ?> (<?= htmlspecialchars($p['destination']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Star Rating</label>
                <select name="rating" required>
                    <option value="5">★★★★★ (5 - Excellent)</option>
                    <option value="4">★★★★☆ (4 - Very Good)</option>
                    <option value="3">★★★☆☆ (3 - Good)</option>
                    <option value="2">★★☆☆☆ (2 - Fair)</option>
                    <option value="1">★☆☆☆☆ (1 - Poor)</option>
                </select>

                <label>Your Experience & Feedback</label>
                <textarea name="comment" rows="3" placeholder="Share what you enjoyed about the safari, guide, and destination..." required></textarea>

                <button type="submit" name="submit_review">Submit Review</button>
            </form>
        </div>
    <?php elseif (!isset($_SESSION['user_id'])): ?>
        <p style="margin-top:20px;"><a href="login.php" class="btn">Login as a Tourist to Leave a Review</a></p>
    <?php endif; ?>

    <!-- Display All Reviews -->
    <h3 style="margin-top: 28px;">Recent Traveller Feedback</h3>
    <?php foreach ($allReviews as $rev): ?>
        <div class="review-box">
            <div style="display:flex; justify-content:space-between;">
                <strong><?= htmlspecialchars($rev['package_title']) ?></strong>
                <span class="stars"><?= str_repeat('★', (int)$rev['rating']) . str_repeat('☆', 5 - (int)$rev['rating']) ?></span>
            </div>
            <p style="margin: 8px 0;"><?= htmlspecialchars($rev['comment']) ?></p>
            <small style="color:#666;">
                Reviewed by <strong><?= htmlspecialchars($rev['full_name']) ?></strong> on <?= date('M d, Y', strtotime($rev['created_at'])) ?>
            </small>
        </div>
    <?php endforeach; ?>
</div>
<?php include'footer.php';?>
</body>
</html>
</body>
</html>
