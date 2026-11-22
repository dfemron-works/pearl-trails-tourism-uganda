<?php
require 'db.php';
require_once 'currency_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$bookingId = (int)($_GET['booking_id'] ?? $_POST['booking_id'] ?? 0);

// Fetch Booking Details
$stmt = $pdo->prepare("
    SELECT b.*, t.title, t.destination 
    FROM bookings b
    JOIN tour_packages t ON b.package_id = t.package_id
    WHERE b.booking_id = ? AND b.user_id = ?
");
$stmt->execute([$bookingId, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    die("Booking not found or unauthorized access.");
}

// Calculate Total Already Paid (Verified + Pending Verification) & Remaining Balance
$paidStmt = $pdo->prepare("SELECT COALESCE(SUM(amount_paid_usd), 0) FROM payments WHERE booking_id = ? AND payment_status != 'Rejected'");
$paidStmt->execute([$bookingId]);
$alreadyPaidUsd = (float)$paidStmt->fetchColumn();
$totalCostUsd = (float)$booking['total_cost'];
$remainingBalanceUsd = max(0, $totalCostUsd - $alreadyPaidUsd);

// Company Destination Accounts Registry
$COMPANY_ACCOUNTS = [
    'MTN Mobile Money'        => ['cat' => 'Mobile Money & Wallets',   'dest' => 'MTN MoMo: +256 772 845 910 (Pearl Trails Uganda Ltd)'],
    'Airtel Money'            => ['cat' => 'Mobile Money & Wallets',   'dest' => 'Airtel Money: +256 752 845 910 (Pearl Trails Uganda Ltd)'],
    'MTN MoMo Merchant Code'  => ['cat' => 'Merchant & App Codes',     'dest' => 'MTN MoMo Pay Merchant Code: 482910 (Pearl Trails)'],
    'Airtel Money Code'       => ['cat' => 'Merchant & App Codes',     'dest' => 'Airtel Pay Merchant Code: 619044 (Pearl Trails)'],
    'M-Pesa (Safaricom)'      => ['cat' => 'Mobile Money & Wallets',   'dest' => 'M-Pesa PayBill: 880120 | Account: PEARL256'],
    'EVC Plus (Hormuud)'      => ['cat' => 'Mobile Money & Wallets',   'dest' => 'EVC Plus Merchant: +252 61 584 5910 (Pearl Trails)'],
    'Wazalendo App'           => ['cat' => 'Merchant & App Codes',     'dest' => 'Wazalendo Mobile App Transfer -> A/C: 0140098234101'],
    'PayPal'                  => ['cat' => 'International Remittance', 'dest' => 'PayPal Business Email: payments@pearltrails.ug'],
    'MoneyGram'               => ['cat' => 'International Remittance', 'dest' => 'MoneyGram Receiver: Pearl Trails Finance Desk, Kampala, Uganda'],
    'Stanbic Bank Uganda'     => ['cat' => 'Bank Transfer / EFT',      'dest' => 'Stanbic Bank | A/C: 9030018472910 | SWIFT: SBICUGKX'],
    'Centenary Bank'          => ['cat' => 'Bank Transfer / EFT',      'dest' => 'Centenary Bank | A/C: 3100084921 | SWIFT: CERBUGKA'],
    'Ecobank Uganda'          => ['cat' => 'Bank Transfer / EFT',      'dest' => 'Ecobank | A/C: 7210049812 | SWIFT: ECOCUGKA'],
    'Pearl Bank'              => ['cat' => 'Bank Transfer / EFT',      'dest' => 'Pearl Bank | A/C: 10402984110 | Branch: Kampala Main'],
    'Barclays Bank'           => ['cat' => 'Bank Transfer / EFT',      'dest' => 'Barclays Bank | A/C: 0341092847 | SWIFT: BARCUGKX'],
    'Absa Bank Uganda'        => ['cat' => 'Bank Transfer / EFT',      'dest' => 'Absa Bank Uganda | A/C: 6004829104 | SWIFT: BARCUGKX'],
    'Wazalendo SACCO'         => ['cat' => 'Bank Transfer / EFT',      'dest' => 'Wazalendo SACCO | A/C: 0140098234101 (Pearl Trails Tourism)'],
];

$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    $method = $_POST['payment_method'] ?? '';
    $paymentPlan = $_POST['payment_plan'] ?? 'FULL'; // 'FULL' or 'DEPOSIT'
    $payerAccount = trim($_POST['payer_account']);
    $txRef = strtoupper(trim($_POST['transaction_ref']));

    // Calculate USD amount being paid based on Deposit vs Full choice
    if ($paymentPlan === 'DEPOSIT') {
        $customDepositLocal = (float)($_POST['deposit_amount_local'] ?? 0);
        $amountPayingUsd = ($currentRate > 0) ? ($customDepositLocal / $currentRate) : $customDepositLocal;
    } else {
        $amountPayingUsd = $remainingBalanceUsd;
    }

    if (!isset($COMPANY_ACCOUNTS[$method])) {
        $feedback = "Error: Please select a valid payment avenue.";
    } elseif ($amountPayingUsd <= 0 || $amountPayingUsd > ($remainingBalanceUsd + 0.5)) {
        $feedback = "Error: Payment amount must be greater than 0 and cannot exceed the remaining balance of " . formatMoney($remainingBalanceUsd) . ".";
    } elseif ($payerAccount === '' || $txRef === '') {
        $feedback = "Error: Sender Phone/Account and Transaction Reference ID are required.";
    } else {
        $meta = $COMPANY_ACCOUNTS[$method];
        $formattedAmount = formatMoney($amountPayingUsd);
        $planLabel = ($paymentPlan === 'DEPOSIT') ? 'Deposit Payment' : 'Full Balance Payment';

        try {
            $ins = $pdo->prepare("
                INSERT INTO payments (
                    booking_id, user_id, payment_category, payment_method,
                    company_destination_account, payer_account_or_phone,
                    transaction_reference, amount_paid_usd, currency_used, amount_in_currency
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $ins->execute([
                $bookingId,
                $_SESSION['user_id'],
                $meta['cat'],
                $method . " ($planLabel)",
                $meta['dest'],
                $payerAccount,
                $txRef,
                round($amountPayingUsd, 2),
                $activeCurrency,
                $formattedAmount
            ]);

            $upd = $pdo->prepare("UPDATE bookings SET status_reason = ? WHERE booking_id = ?");
            $upd->execute(["$planLabel of $formattedAmount submitted via $method (Ref: $txRef) - Awaiting Admin Verification", $bookingId]);

            triggerAdminAlert(
                $pdo,
                'USER_INQUIRY',
                'INFO',
                "💳 New $planLabel ($method)",
                "{$_SESSION['full_name']} submitted $formattedAmount for Booking #$bookingId via $method (Ref: $txRef).",
                $method
            );

            // Refresh remaining balance after insert
            $alreadyPaidUsd += $amountPayingUsd;
            $remainingBalanceUsd = max(0, $totalCostUsd - $alreadyPaidUsd);
            $feedback = "Success! Your $planLabel ($formattedAmount, Ref: $txRef) has been sent to Pearl Trails Uganda for verification.";
        } catch (PDOException $e) {
            $feedback = "Error: Transaction Reference '$txRef' has already been used.";
        }
    }
}

$payHistory = $pdo->prepare("SELECT * FROM payments WHERE booking_id = ? ORDER BY paid_at DESC");
$payHistory->execute([$bookingId]);
$payments = $payHistory->fetchAll();

// Suggested 30% Deposit in Active Currency
$suggestedDepositLocal = round(($remainingBalanceUsd * 0.30) * $currentRate, in_array($activeCurrency, ['UGX', 'TZS', 'RWF'], true) ? 0 : 2);
$fullBalanceLocal = round($remainingBalanceUsd * $currentRate, in_array($activeCurrency, ['UGX', 'TZS', 'RWF'], true) ? 0 : 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Electronic Payment Checkout - Pearl Trails Uganda</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dest-box { background: var(--bg-dark); border: 2px dashed var(--accent-neon); border-radius: 8px; padding: 14px; margin: 10px 0 16px; color: var(--accent-neon); font-weight: bold; font-size: 15px; }
        .bal-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-top: 10px; }
        .bal-item { background: var(--bg-dark); padding: 12px; border-radius: 6px; border: 1px solid var(--border-color); }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container" style="max-width: 840px;">
    <h2>💳 Electronic Funds Transfer &amp; Mobile Wallet Checkout</h2>

    <?php if ($feedback): ?>
        <div class="alert <?= strpos($feedback, 'Error') === 0 ? 'error' : '' ?>"><?= htmlspecialchars($feedback) ?></div>
    <?php endif; ?>

    <div class="card" style="margin-bottom: 20px;">
        <h3 style="margin-top:0; color:var(--accent-neon);">Booking #<?= (int)$booking['booking_id'] ?> — <?= htmlspecialchars($booking['title']) ?></h3>
        <p style="color:var(--text-muted); margin:4px 0;">Destination: <?= htmlspecialchars($booking['destination']) ?> &bull; Date: <?= htmlspecialchars($booking['travel_date']) ?> &bull; Guests: <?= (int)$booking['num_guests'] ?></p>
        <div class="bal-grid">
            <div class="bal-item"><small style="color:var(--text-muted);">Total Safari Cost</small><br><strong><?= formatMoney($totalCostUsd) ?></strong></div>
            <div class="bal-item"><small style="color:var(--text-muted);">Amount Paid / Submitted</small><br><strong style="color:#ffb703;"><?= formatMoney($alreadyPaidUsd) ?></strong></div>
            <div class="bal-item"><small style="color:var(--text-muted);">Remaining Balance Due</small><br><strong style="color:var(--accent-neon); font-size:18px;"><?= formatMoney($remainingBalanceUsd) ?></strong></div>
        </div>
    </div>

    <?php if ($remainingBalanceUsd > 0.01): ?>
    <div class="card">
        <h3 style="margin-top:0;">Make Deposit or Full Payment</h3>
        <form method="POST">
            <input type="hidden" name="booking_id" value="<?= $bookingId ?>">

            <label>1. Choose Payment Option (Full Payment or Partial Deposit) *</label>
            <select name="payment_plan" id="paymentPlanSelect" onchange="toggleDepositInput()" required>
                <option value="FULL">Pay Full Balance (<?= formatMoney($remainingBalanceUsd) ?>)</option>
                <option value="DEPOSIT">Pay Booking Deposit (Minimum 30% = <?= formatMoney($remainingBalanceUsd * 0.30) ?>)</option>
            </select>

            <div id="depositAmountBox" style="display:none;">
                <label>Enter Deposit Amount to Pay Now (<?= htmlspecialchars($activeCurrency) ?>) *</label>
                <input type="number" step="any" name="deposit_amount_local" min="1" max="<?= htmlspecialchars($fullBalanceLocal) ?>" value="<?= htmlspecialchars($suggestedDepositLocal) ?>">
            </div>

            <label>2. Select Company Bank Account, Mobile Money Wallet, or App Code *</label>
            <select name="payment_method" id="paymentMethodSelect" onchange="updateDestinationDetails()" required>
                <optgroup label="📱 Mobile Money &amp; Merchant Codes">
                    <option value="MTN Mobile Money">MTN Mobile Money</option>
                    <option value="Airtel Money">Airtel Money</option>
                    <option value="MTN MoMo Merchant Code">MTN MoMo Merchant Code</option>
                    <option value="Airtel Money Code">Airtel Money Merchant Code</option>
                    <option value="M-Pesa (Safaricom)">M-Pesa</option>
                    <option value="EVC Plus (Hormuud)">EVC Plus</option>
                    <option value="Wazalendo App">Wazalendo Mobile App</option>
                </optgroup>
                <optgroup label="🌍 International Remittance &amp; Online">
                    <option value="PayPal">PayPal</option>
                    <option value="MoneyGram">MoneyGram</option>
                </optgroup>
                <optgroup label="🏦 Company Bank Accounts &amp; SACCO">
                    <option value="Stanbic Bank Uganda">Stanbic Bank</option>
                    <option value="Centenary Bank">Centenary Bank</option>
                    <option value="Ecobank Uganda">Ecobank</option>
                    <option value="Pearl Bank">Pearl Bank</option>
                    <option value="Barclays Bank">Barclays Bank</option>
                    <option value="Absa Bank Uganda">Absa Bank</option>
                    <option value="Wazalendo SACCO">Wazalendo SACCO</option>
                </optgroup>
            </select>

            <div class="dest-box" id="companyDestDisplay">
                Send Payment To: MTN MoMo: +256 772 845 910 (Pearl Trails Uganda Ltd)
            </div>

            <label>3. Your Sender Mobile Number / Bank Account / Email *</label>
            <input type="text" name="payer_account" placeholder="e.g., +256 772 123456 or Bank Account Number" required>

            <label>4. Transaction Reference ID / Receipt Number *</label>
            <input type="text" name="transaction_ref" placeholder="e.g., MP261117.1305.A92814 or Bank EFT Ref" required>

            <button type="submit" name="submit_payment" style="width:100%;">Submit Payment Receipt</button>
        </form>
    </div>
    <?php else: ?>
        <div class="alert">✅ This booking is 100% paid in full! View your payment receipts below.</div>
    <?php endif; ?>

    <?php if (!empty($payments)): ?>
        <h3 style="margin-top:26px;">Payment Receipts History for Booking #<?= $bookingId ?></h3>
        <table>
            <tr><th>Payment Method</th><th>Amount Paid</th><th>Sender Account</th><th>Transaction Ref</th><th>Status</th></tr>
            <?php foreach ($payments as $pay): ?>
            <tr>
                <td><strong><?= htmlspecialchars($pay['payment_method']) ?></strong></td>
                <td style="color:var(--accent-neon); font-weight:bold;"><?= htmlspecialchars($pay['amount_in_currency']) ?></td>
                <td><?= htmlspecialchars($pay['payer_account_or_phone']) ?></td>
                <td><code><?= htmlspecialchars($pay['transaction_reference']) ?></code></td>
                <td><strong><?= htmlspecialchars($pay['payment_status']) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<script>
const companyDestinations = <?= json_encode(array_map(function($item) { return $item['dest']; }, $COMPANY_ACCOUNTS), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

function updateDestinationDetails() {
    const method = document.getElementById('paymentMethodSelect').value;
    const box = document.getElementById('companyDestDisplay');
    if (box && companyDestinations[method]) {
        box.textContent = 'Send Payment To: ' + companyDestinations[method];
    }
}

function toggleDepositInput() {
    const plan = document.getElementById('paymentPlanSelect').value;
    document.getElementById('depositAmountBox').style.display = (plan === 'DEPOSIT') ? 'block' : 'none';
}
</script>
<?php include 'footer.php'; ?>
</body>
</html>
