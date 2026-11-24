<?php
ob_start();
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

require_once 'db.php';
if (file_exists(__DIR__ . '/alerts_helper.php')) {
    require_once __DIR__ . '/alerts_helper.php';
}

if (ob_get_length()) {
    ob_clean();
}

define('MOMO_WEBHOOK_SECRET', 'PT_UG_MOMO_AIRTEL_SECRET_2026');

$rawBody = file_get_contents('php://input');
$data = json_decode($rawBody, true);
if (!is_array($data)) {
    $data = $_POST;
}

$sig = $_SERVER['HTTP_X_WEBHOOK_SECRET'] ?? $_GET['secret'] ?? '';
if ($sig !== MOMO_WEBHOOK_SECRET) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid Webhook Secret Signature']);
    exit;
}

$provider    = strtoupper(trim($data['provider'] ?? 'MTN_MOMO'));
$externalRef = trim($data['financialTransactionId'] ?? $data['transaction_id'] ?? ('TXN-' . time()));
$bookingId   = (int)($data['externalId'] ?? $data['booking_id'] ?? 0);
$amountPaid  = (float)($data['amount'] ?? 0);
$currency    = strtoupper(trim($data['currency'] ?? 'UGX'));
$payerMsisdn = trim($data['payer']['partyId'] ?? $data['msisdn'] ?? 'Unknown');
$txStatus    = strtoupper(trim($data['status'] ?? 'SUCCESSFUL'));

if ($bookingId <= 0 || $amountPaid <= 0) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'Missing valid booking_id (externalId) or amount']);
    exit;
}

if (in_array($txStatus, ['SUCCESSFUL', 'SUCCESS', 'TS'], true)) {
    try {
        $pdo->beginTransaction();
        $amountUsd = ($currency === 'UGX') ? round($amountPaid / 3700, 2) : $amountPaid;

        $payStmt = $pdo->prepare("INSERT INTO payments (booking_id, amount_paid, payment_method, transaction_ref, payment_status) VALUES (?, ?, ?, ?, 'Verified')");
        $payStmt->execute([$bookingId, $amountUsd, "$provider ($payerMsisdn)", $externalRef]);

        $updStmt = $pdo->prepare("UPDATE bookings SET status = 'Confirmed', status_reason = ? WHERE booking_id = ?");
        $updStmt->execute(["Auto-confirmed via $provider PIN Approval (Ref: $externalRef)", $bookingId]);

        $ledStmt = $pdo->prepare("INSERT INTO financial_ledger (entry_date, account_type, description, amount_usd) VALUES (CURDATE(), 'Revenue', ?, ?)");
        $ledStmt->execute(["$provider Collection for Booking #$bookingId ($payerMsisdn)", $amountUsd]);

        $pdo->commit();

        if (function_exists('triggerAdminAlert')) {
            triggerAdminAlert($pdo, 'PAYMENT_WEBHOOK', 'INFO', "💰 $provider Payment Auto-Confirmed", "Booking #$bookingId paid $currency " . number_format($amountPaid) . " ($payerMsisdn) • Ref: $externalRef", "$provider Gateway");
        }

        echo json_encode(['status' => 'accepted', 'booking_id' => $bookingId, 'booking_status' => 'Confirmed', 'transaction_ref' => $externalRef]);
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database transaction failed']);
        exit;
    }
}

echo json_encode(['status' => 'ignored', 'received_status' => $txStatus]);
