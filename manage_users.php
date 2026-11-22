<?php
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';

// 1. Update User Role (Promote / Demote) & Account Status (Active / Suspended / Flagged)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $targetId = (int)$_POST['user_id'];
    $newRole = $_POST['role'];
    $newStatus = $_POST['account_status'];
    $flagReason = trim($_POST['flag_reason']);

    if ($targetId === (int)$_SESSION['user_id']) {
        $message = "Error: You cannot demote or suspend your own active administrator account.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET role = ?, account_status = ?, flag_reason = ? WHERE user_id = ?");
        $stmt->execute([$newRole, $newStatus, $flagReason !== '' ? $flagReason : null, $targetId]);
        $message = "User #$targetId updated (Role: $newRole | Status: $newStatus).";
    }
}

// 2. Delete User Account
if (isset($_GET['delete_user'])) {
    $targetId = (int)$_GET['delete_user'];
    if ($targetId === (int)$_SESSION['user_id']) {
        $message = "Error: You cannot delete your own logged-in account.";
    } else {
        $del = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $del->execute([$targetId]);
        $message = "User account #$targetId has been permanently deleted.";
    }
}

// Search Filter for Admin User Directory
$searchUser = trim($_GET['search_user'] ?? '');
if ($searchUser !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM users 
        WHERE full_name LIKE ? OR email LIKE ? OR id_number LIKE ? OR nationality LIKE ? OR designation LIKE ?
        ORDER BY user_id ASC
    ");
    $like = "%$searchUser%";
    $stmt->execute([$like, $like, $like, $like, $like]);
    $users = $stmt->fetchAll();
} else {
    $users = $pdo->query("SELECT * FROM users ORDER BY user_id ASC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - Pearl Trails T & T Uganda Ltd</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .status-Active { color: var(--accent-neon); font-weight: bold; }
        .status-Flagged { color: var(--warn-neon); font-weight: bold; }
        .status-Suspended { color: var(--danger-neon); font-weight: bold; }
        .detail-label { color: var(--text-muted); font-size: 12px; display: block; margin-top: 3px; }
        .table-wrap { overflow-x: auto; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container" style="max-width: 1320px;">
    <h2>System Administrator: Client Profiles, Roles & Rights </h2>

    <?php if ($message): ?>
        <div class="alert <?= strpos($message, 'Error') === 0 ? 'error' : '' ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Search Users by Name, NIN/Passport, Nationality, or Designation -->
    <form method="GET" style="display:flex; gap:10px; margin:16px 0;">
        <input type="text" name="search_user" placeholder="Search by name, NIN/Passport, nationality, or designation..." value="<?= htmlspecialchars($searchUser) ?>" style="margin:0;">
        <button type="submit">Search Users</button>
        <?php if ($searchUser !== ''): ?>
            <a href="manage_users.php" class="btn btn-edit">Reset</a>
        <?php endif; ?>
    </form>

    <div class="table-wrap">
        <table>
            <tr>
                <th>ID & Personal Info</th>
                <th>Nationality & ID (NIN / Passport)</th>
                <th>Contacts (Mobile & Office)</th>
                <th>Designation & Role</th>
                <th>Account Status</th>
                <th>Promote / Demote, Flag & Suspend</th>
                <th>Delete</th>
            </tr>
            <?php foreach ($users as $u): ?>
            <tr>
                <td>
                    <strong>#<?= (int)$u['user_id'] ?> — <?= htmlspecialchars($u['full_name']) ?></strong>
                    <span class="detail-label">DOB: <?= htmlspecialchars($u['dob'] ?? 'Not Provided') ?></span>
                    <span class="detail-label">
                        Gender: <?= htmlspecialchars($u['gender'] ?? 'N/A') ?>
                        <?= !empty($u['marital_status']) ? ' | ' . htmlspecialchars($u['marital_status']) : '' ?>
                    </span>
                </td>

                <td>
                    <strong><?= htmlspecialchars($u['nationality'] ?? 'N/A') ?></strong>
                    <span class="detail-label">ID: <?= htmlspecialchars($u['id_number'] ?? 'Not Provided') ?></span>
                </td>

                <td>
                    <span>Email: <?= htmlspecialchars($u['email']) ?></span>
                    <span class="detail-label">Mobile: <?= htmlspecialchars($u['mobile_contact'] ?? 'N/A') ?></span>
                    <?php if (!empty($u['office_line'])): ?>
                        <span class="detail-label">Office: <?= htmlspecialchars($u['office_line']) ?></span>
                    <?php endif; ?>
                </td>

                <td>
                    <strong><?= htmlspecialchars($u['designation'] ?? 'Tourist / Client') ?></strong>
                    <span class="detail-label" style="color:var(--accent-neon);">Role: <?= strtoupper(htmlspecialchars($u['role'])) ?></span>
                </td>

                <td>
                    <span class="status-<?= htmlspecialchars($u['account_status']) ?>"><?= htmlspecialchars($u['account_status']) ?></span>
                    <?php if (!empty($u['flag_reason'])): ?>
                        <span class="detail-label" style="color:var(--warn-neon);">Reason: <?= htmlspecialchars($u['flag_reason']) ?></span>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if ((int)$u['user_id'] === (int)$_SESSION['user_id']): ?>
                        <em>Current Admin Session</em>
                    <?php else: ?>
                        <form method="POST" style="display:flex; flex-direction:column; gap:5px; margin:0; min-width:190px;">
                            <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                            <select name="role" style="margin:0; padding:6px;" title="Promote or Demote">
                                <option value="tourist" <?= $u['role'] === 'tourist' ? 'selected' : '' ?>>Tourist (Standard)</option>
                                <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin (Promoted)</option>
                            </select>
                            <select name="account_status" style="margin:0; padding:6px;" title="Account Status">
                                <option value="Active" <?= $u['account_status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Flagged" <?= $u['account_status'] === 'Flagged' ? 'selected' : '' ?>>Flag Off (Warning)</option>
                                <option value="Suspended" <?= $u['account_status'] === 'Suspended' ? 'selected' : '' ?>>Suspend Account</option>
                            </select>
                            <input type="text" name="flag_reason" placeholder="Reason for flag/suspension..." value="<?= htmlspecialchars($u['flag_reason'] ?? '') ?>" style="margin:0; padding:6px;">
                            <button type="submit" name="update_user" style="padding:6px 10px;">Apply Changes</button>
                        </form>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if ((int)$u['user_id'] !== (int)$_SESSION['user_id']): ?>
                        <a href="?delete_user=<?= (int)$u['user_id'] ?>" class="btn btn-danger" style="padding:6px 10px;" onclick="return confirm('Permanently delete user <?= htmlspecialchars($u['full_name']) ?> and all their bookings?')">Delete</a>
                    <?php else: ?> - <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>
