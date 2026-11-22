<?php
require 'db.php';
$feedback = '';

// 1. Admin Upload Photo to Gallery
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_photo'])) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit;
    }

    $caption = trim($_POST['caption']);
    $packageId = !empty($_POST['package_id']) ? (int)$_POST['package_id'] : null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt, true)) {
            $feedback = "Error: Only JPG, PNG, and WEBP image files are allowed.";
        } else {
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }
            $newFileName = 'safari_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $targetPath = 'uploads/' . $newFileName;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $stmt = $pdo->prepare("INSERT INTO gallery (package_id, caption, image_path) VALUES (?, ?, ?)");
                $stmt->execute([$packageId, $caption, $targetPath]);
                $feedback = "Success! Photo uploaded to the gallery.";
            } else {
                $feedback = "Error: Failed to move uploaded image file.";
            }
        }
    } else {
        $feedback = "Error: Please select a valid image file to upload.";
    }
}

// 2. Admin Delete Photo
if (isset($_GET['delete_photo']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $photoId = (int)$_GET['delete_photo'];
    $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE photo_id = ?");
    $stmt->execute([$photoId]);
    $photo = $stmt->fetch();

    if ($photo) {
        if (file_exists($photo['image_path'])) {
            unlink($photo['image_path']);
        }
        $del = $pdo->prepare("DELETE FROM gallery WHERE photo_id = ?");
        $del->execute([$photoId]);
        $feedback = "Photo removed from gallery.";
    }
}

// Fetch Packages for Dropdown & Filter
$packages = $pdo->query("SELECT package_id, title FROM tour_packages ORDER BY title ASC")->fetchAll();

// Filter Gallery by Package if requested
$filterPkg = (int)($_GET['package_id'] ?? 0);
if ($filterPkg > 0) {
    $galStmt = $pdo->prepare("
        SELECT g.*, t.title AS package_title, t.destination
        FROM gallery g
        LEFT JOIN tour_packages t ON g.package_id = t.package_id
        WHERE g.package_id = ?
        ORDER BY g.uploaded_at DESC
    ");
    $galStmt->execute([$filterPkg]);
} else {
    $galStmt = $pdo->query("
        SELECT g.*, t.title AS package_title, t.destination
        FROM gallery g
        LEFT JOIN tour_packages t ON g.package_id = t.package_id
        ORDER BY g.uploaded_at DESC
    ");
}
$photos = $galStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Safari & Product Gallery - Pearl Trails Uganda</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px; margin-top: 20px; }
        .photo-card { border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.06); }
        .photo-card img { width: 100%; height: 200px; object-fit: cover; cursor: pointer; display: block; transition: transform 0.2s; }
        .photo-card img:hover { transform: scale(1.03); }
        .photo-info { padding: 12px; }
        /* Lightbox Modal */
        #lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); justify-content: center; align-items: center; flex-direction: column; z-index: 1000; padding: 20px; }
        #lightbox img { max-width: 90%; max-height: 80vh; border-radius: 6px; }
        #lightbox p { color: #fff; margin-top: 12px; font-size: 16px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <strong>Pearl Trails Uganda — Safari Gallery</strong>
    <nav>
        <a href="index.php">Packages</a>
        <a href="gallery.php">Gallery</a>
        <a href="reviews.php">Feedback</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.php">Records Manager</a>
                <a href="manage_users.php">Users</a>
                <a href="reports.php">Reports</a>
            <?php else: ?>
                <a href="my_bookings.php">My Bookings</a>
            <?php endif; ?>
            <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['full_name']) ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>


<div class="container">
    <h2>Uganda Safari & Tour Product Gallery</h2>
    <p>Click any photograph to view full screen.</p>

    <?php if ($feedback): ?>
        <div class="alert <?= strpos($feedback, 'Error') === 0 ? 'error' : '' ?>"><?= htmlspecialchars($feedback) ?></div>
    <?php endif; ?>

    <!-- Admin Upload Form -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="card" style="margin-bottom: 20px;">
            <h3>Upload New Safari / Product Picture</h3>
            <form method="POST" enctype="multipart/form-data" class="grid">
                <div>
                    <label>Select Image (JPG, PNG, WEBP)</label>
                    <input type="file" name="photo" accept="image/*" required>
                </div>
                <div>
                    <label>Photo Caption / Description</label>
                    <input type="text" name="caption" placeholder="e.g., Silverback Gorilla in Bwindi Mist" required>
                </div>
                <div>
                    <label>Link to Safari Package (Optional)</label>
                    <select name="package_id">
                        <option value="">-- General Showcase --</option>
                        <?php foreach ($packages as $pkg): ?>
                            <option value="<?= (int)$pkg['package_id'] ?>"><?= htmlspecialchars($pkg['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display:flex; align-items:flex-end;">
                    <button type="submit" name="upload_photo">Upload Picture</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Filter by Package -->
    <form method="GET" style="display:flex; gap:10px; align-items:center;">
        <select name="package_id" style="max-width:320px; margin:0;">
            <option value="0">All Safari Destinations & Products</option>
            <?php foreach ($packages as $pkg): ?>
                <option value="<?= (int)$pkg['package_id'] ?>" <?= $filterPkg === (int)$pkg['package_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pkg['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter Gallery</button>
    </form>

    <!-- Gallery Grid -->
    <div class="gallery-grid">
        <?php if (empty($photos)): ?>
            <p>No pictures uploaded yet. Log in as Admin to upload safari photos.</p>
        <?php else: ?>
            <?php foreach ($photos as $ph): ?>
                <div class="photo-card">
                    <img src="<?= htmlspecialchars($ph['image_path']) ?>" alt="<?= htmlspecialchars($ph['caption']) ?>" onclick="openLightbox(this.src, '<?= htmlspecialchars(addslashes($ph['caption'])) ?>')">
                    <div class="photo-info">
                        <strong><?= htmlspecialchars($ph['caption']) ?></strong>
                        <?php if (!empty($ph['package_title'])): ?>
                            <p style="margin:5px 0; font-size:13px; color:#2d6a4f;">
                                Package: <?= htmlspecialchars($ph['package_title']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="?delete_photo=<?= (int)$ph['photo_id'] ?>" class="btn btn-danger" style="padding:5px 10px; font-size:12px; margin-top:6px;" onclick="return confirm('Delete this photo?')">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Lightbox Fullscreen Viewer -->
<div id="lightbox" onclick="this.style.display='none'">
    <img id="lightboxImg" src="" alt="Full View">
    <p id="lightboxCaption"></p>
</div>

<script>
function openLightbox(src, caption) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxCaption').textContent = caption;
    document.getElementById('lightbox').style.display = 'flex';
}
</script>
</body>
</html>
