<?php
// Reusable Function: Trigger a Real-Time Admin Alert
function triggerAdminAlert(PDO $pdo, string $type, string $severity, string $title, string $message, string $channel = 'Web App'): void {
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_alerts (alert_type, severity, title, message, source_channel) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$type, $severity, $title, $message, $channel]);
    } catch (PDOException $e) {
        // Fail silently so user flow is never interrupted
    }
}

// Automated Violent & Abusive Language Scanner
function checkAndAlertAbusiveLanguage(PDO $pdo, string $text, string $contextLabel = 'User Input', ?int $userId = null): bool {
    $badPatterns = [
        'kill', 'murder', 'bomb', 'attack', 'shoot', 'terror', 'threat', 'stab',
        'idiot', 'stupid', 'bastard', 'fuck', 'shit', 'bitch', 'asshole', 'scam', 'fraud', 'thief'
    ];

    $lowerText = strtolower($text);
    $matchedWords = [];

    foreach ($badPatterns as $word) {
        if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $lowerText)) {
            $matchedWords[] = $word;
        }
    }

    if (!empty($matchedWords)) {
        $flaggedList = implode(', ', array_unique($matchedWords));
        $userInfo = $userId ? "User ID #$userId" : "Guest Visitor";
        $alertMsg = "$userInfo used restricted/abusive language ($flaggedList) in $contextLabel: \"" . mb_substr($text, 0, 140) . "\"";

        triggerAdminAlert(
            $pdo,
            'ABUSE_DETECTED',
            'DANGER',
            '🚨 Violent / Abusive Language Detected!',
            $alertMsg,
            $contextLabel
        );

        // Automatically flag registered user account for Admin review
        if ($userId) {
            $flagStmt = $pdo->prepare("UPDATE users SET account_status = 'Flagged', flag_reason = ? WHERE user_id = ? AND role != 'admin'");
            $flagStmt->execute(["Auto-flagged for abusive language ($flaggedList) in $contextLabel", $userId]);
        }
        return true;
    }
    return false;
}

// AJAX Live Polling Endpoint for Logged-In Admins
if (isset($_GET['alerts_api'])) {
    if (!isset($pdo)) {
        require_once 'db.php';
    }
    header('Content-Type: application/json');

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['unread_count' => 0, 'alerts' => []]);
        exit;
    }

    if ($_GET['alerts_api'] === 'mark_read') {
        $pdo->exec("UPDATE admin_alerts SET is_read = 1 WHERE is_read = 0");
        echo json_encode(['status' => 'ok']);
        exit;
    }

    $unreadCount = (int)$pdo->query("SELECT COUNT(*) FROM admin_alerts WHERE is_read = 0")->fetchColumn();
    $latestAlerts = $pdo->query("SELECT * FROM admin_alerts ORDER BY alert_id DESC LIMIT 12")->fetchAll();

    echo json_encode([
        'unread_count' => $unreadCount,
        'alerts' => $latestAlerts
    ]);
    exit;
}
?>
