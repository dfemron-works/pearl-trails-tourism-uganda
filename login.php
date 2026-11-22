<?php
require 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        if ($user['account_status'] === 'Suspended') {
            $reason = !empty($user['flag_reason']) ? $user['flag_reason'] : 'Account suspended by System Administrator.';
            $error = "Access Denied: Your account is suspended. Reason: " . $reason;
        } else {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['account_status'] = $user['account_status'];
            $_SESSION['flag_reason'] = $user['flag_reason'];

            // Record Login Event for Hour/Day/Week/Month/Year Analytics
            $loginLog = $pdo->prepare("INSERT INTO system_logs (event_type, user_id, page_url, ip_address) VALUES ('login', ?, 'login.php', ?)");
            $loginLog->execute([
                $user['user_id'],
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            header("Location: " . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'index.php'));
            exit;
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Pearl Trails Uganda</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container" style="max-width:440px;">
    <h2>System Login</h2>
    <p style="color:var(--text-muted); margin-top:-6px;">Sign in to book safaris, manage itineraries, or access administrative controls.</p>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="e.g., admin@pearltrails.ug" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>

        <button type="submit" style="width:100%; margin-top:6px;">Login to System</button>
    </form>

    <p style="margin-top:16px; text-align:center; font-size:14px;">
        Don't have an account? <a href="register.php" style="color:var(--accent-neon); font-weight:bold;">Register here</a>
    </p>
</div>
</body>
</html>
