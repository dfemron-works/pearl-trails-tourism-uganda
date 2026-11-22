<?php
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM tour_packages WHERE package_id = ?");
$stmt->execute([$id]);
$pkg = $stmt->fetch();

if (!$pkg) {
    die("Package not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $discount = max(0, min(90, (int)($_POST['discount_percent'] ?? 0)));
    $upd = $pdo->prepare("UPDATE tour_packages SET title=?, destination=?, duration_days=?, price_usd=?, discount_percent=?, max_capacity=?, description=? WHERE package_id=?");
    $upd->execute([
        trim($_POST['title']),
        trim($_POST['destination']),
        (int)$_POST['duration_days'],
        (float)$_POST['price_usd'],
        $discount,
        (int)$_POST['max_capacity'],
        trim($_POST['description']),
        $id
    ]);
    header("Location: admin_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Package & Promotion - Pearl Trails Uganda</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container" style="max-width:650px;">
    <h2>Update Safari Package #<?= (int)$pkg['package_id'] ?></h2>
    <form method="POST">
        <label>Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($pkg['title']) ?>" required>

        <label>Destination</label>
        <input type="text" name="destination" value="<?= htmlspecialchars($pkg['destination']) ?>" required>

        <label>Duration (Days)</label>
        <input type="number" name="duration_days" min="1" value="<?= (int)$pkg['duration_days'] ?>" required>

        <label>Base Price per Person (USD $)</label>
        <input type="number" step="0.01" name="price_usd" min="1" value="<?= htmlspecialchars($pkg['price_usd']) ?>" required>

        <label>Promotional Offer (% OFF — Set 0 to Remove Offer)</label>
        <input type="number" name="discount_percent" min="0" max="90" value="<?= (int)($pkg['discount_percent'] ?? 0) ?>" required>

        <label>Max Capacity</label>
        <input type="number" name="max_capacity" min="1" value="<?= (int)$pkg['max_capacity'] ?>" required>

        <label>Description</label>
        <textarea name="description" rows="4" required><?= htmlspecialchars($pkg['description']) ?></textarea>

        <div style="display:flex; gap:10px;">
            <button type="submit">Update Package & Offer</button>
            <a href="admin_dashboard.php" class="btn btn-edit">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>

