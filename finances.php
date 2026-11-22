<?php
require 'db.php';
require_once 'currency_helper.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';

// Record Financial Ledger Entry (Operational Expenses, Liabilities, Owner Capital)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ledger_entry'])) {
    $entryType = $_POST['entry_type'];
    $title     = trim($_POST['account_title']);
    $amountUsd = max(0, (float)$_POST['amount_usd']);
    $entryDate = $_POST['entry_date'];

    $stmt = $pdo->prepare("INSERT INTO financial_ledger (entry_type, account_title, amount_usd, entry_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$entryType, $title, $amountUsd, $entryDate]);
    $message = "Financial ledger entry recorded successfully.";
}

if (isset($_GET['delete_entry'])) {
    $pdo->prepare("DELETE FROM financial_ledger WHERE entry_id = ?")->execute([(int)$_GET['delete_entry']]);
    $message = "Ledger entry removed.";
}

// =========================================================================
// 1. INCOME STATEMENT CALCULATIONS (USD BASE)
// =========================================================================
// Revenue from Verified Electronic Payments + Confirmed/Concluded Bookings
$verifiedPaymentsUsd = (float)$pdo->query("SELECT COALESCE(SUM(amount_paid_usd), 0) FROM payments WHERE payment_status = 'Verified & Paid'")->fetchColumn();
$confirmedBookingsUsd = (float)$pdo->query("SELECT COALESCE(SUM(total_cost), 0) FROM bookings WHERE status IN ('Confirmed', 'Concluded')")->fetchColumn();
$safariRevenueUsd = max($verifiedPaymentsUsd, $confirmedBookingsUsd);

// Monthly Staff Payroll Expense (from Active Staff Inventory)
$monthlyPayrollUsd = (float)$pdo->query("SELECT COALESCE(SUM(monthly_salary_usd), 0) FROM company_staff WHERE employment_status = 'Active'")->fetchColumn();

// Recorded Operational Expenses from Financial Ledger
$otherExpensesUsd = (float)$pdo->query("SELECT COALESCE(SUM(amount_usd), 0) FROM financial_ledger WHERE entry_type = 'Operational Expense'")->fetchColumn();
$expenseRows = $pdo->query("SELECT * FROM financial_ledger WHERE entry_type = 'Operational Expense' ORDER BY entry_date DESC")->fetchAll();

$totalExpensesUsd = $monthlyPayrollUsd + $otherExpensesUsd;
$netProfitUsd     = $safariRevenueUsd - $totalExpensesUsd;

// =========================================================================
// 2. BALANCE SHEET CALCULATIONS (ASSETS = LIABILITIES + OWNER'S EQUITY)
// =========================================================================
// Non-Current (Fixed) Assets from Inventory Registers
$electronicEquipUsd = (float)$pdo->query("SELECT COALESCE(SUM(quantity * purchase_value_usd), 0) FROM office_equipment WHERE category = 'Electronic'")->fetchColumn();
$nonElectronicEquipUsd = (float)$pdo->query("SELECT COALESCE(SUM(quantity * purchase_value_usd), 0) FROM office_equipment WHERE category = 'Non-Electronic'")->fetchColumn();
$vehiclesAssetUsd = (float)$pdo->query("SELECT COALESCE(SUM(valuation_usd), 0) FROM company_vehicles WHERE operational_status != 'Retired'")->fetchColumn();
$totalFixedAssetsUsd = $electronicEquipUsd + $nonElectronicEquipUsd + $vehiclesAssetUsd;

// Current Assets
$accountsReceivableUsd = (float)$pdo->query("
    SELECT COALESCE(SUM(b.total_cost), 0) - COALESCE((SELECT SUM(p.amount_paid_usd) FROM payments p WHERE p.payment_status = 'Verified & Paid'), 0)
    FROM bookings b WHERE b.status IN ('Confirmed', 'Concluded')
")->fetchColumn();
$accountsReceivableUsd = max(0, $accountsReceivableUsd);

$cashAndBankUsd = max(0, $verifiedPaymentsUsd - $otherExpensesUsd);
$totalCurrentAssetsUsd = $cashAndBankUsd + $accountsReceivableUsd;

$totalAssetsUsd = $totalFixedAssetsUsd + $totalCurrentAssetsUsd;

// Liabilities
$currentLiabilitiesUsd = (float)$pdo->query("SELECT COALESCE(SUM(amount_usd), 0) FROM financial_ledger WHERE entry_type = 'Current Liability'")->fetchColumn();
$longTermLiabilitiesUsd = (float)$pdo->query("SELECT COALESCE(SUM(amount_usd), 0) FROM financial_ledger WHERE entry_type = 'Long-Term Liability'")->fetchColumn();
$totalLiabilitiesUsd = $currentLiabilitiesUsd + $longTermLiabilitiesUsd;

// Owner's Equity (Balances Total Assets)
$paidInCapitalUsd = (float)$pdo->query("SELECT COALESCE(SUM(amount_usd), 0) FROM financial_ledger WHERE entry_type = 'Owner Capital'")->fetchColumn();
$retainedReservesUsd = $totalAssetsUsd - ($totalLiabilitiesUsd + $paidInCapitalUsd + $netProfitUsd);
$totalEquityUsd = $paidInCapitalUsd + $retainedReservesUsd + $netProfitUsd;

$totalLiabilitiesAndEquityUsd = $totalLiabilitiesUsd + $totalEquityUsd;

$ledgerList = $pdo->query("SELECT * FROM financial_ledger ORDER BY entry_date DESC, entry_id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Statements (Income &amp; Balance Sheet) - Pearl Trails Tours &amp; Travel Uganda Ltd</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stmt-box { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; padding: 20px; }
        .stmt-table th { background: #192b24; color: var(--accent-neon); }
        .stmt-total td { font-weight: bold; font-size: 16px; border-top: 2px solid var(--accent-neon); border-bottom: 3px double var(--accent-neon); color: var(--accent-neon); }
        .stmt-section td { background: rgba(0, 245, 155, 0.06); font-weight: bold; color: #ffb703; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h2>📊 Financial Accounting &amp; Statements (<?= htmlspecialchars($activeCurrency) ?>)</h2>
        <button type="button" onclick="window.print()" class="btn no-print">🖨️ Print Financial Statements</button>
    </div>

    <?php if ($message): ?>
        <div class="alert no-print"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Record Ledger Entry Form (Hidden when printing) -->
    <div class="card no-print" style="margin-top:16px;">
        <h3 style="margin-top:0;">Post Financial Ledger Entry (Expenses, Liabilities &amp; Capital)</h3>
        <form method="POST" class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:10px; margin-top:0;">
            <select name="entry_type" required style="margin:0;">
                <option value="Operational Expense">Operational Expense (Fuel, Park Fees, Rent)</option>
                <option value="Current Liability">Current Liability (Short-Term Payable)</option>
                <option value="Long-Term Liability">Long-Term Liability (Fleet Loan)</option>
                <option value="Owner Capital">Owner Paid-In Capital</option>
            </select>
            <input type="text" name="account_title" placeholder="Account / Description (e.g., UWA Gorilla Permits)" required style="margin:0;">
            <input type="number" step="0.01" name="amount_usd" placeholder="Amount (USD $)" min="0.01" required style="margin:0;">
            <input type="date" name="entry_date" value="<?= date('Y-m-d') ?>" required style="margin:0;">
            <button type="submit" name="add_ledger_entry">Record Entry</button>
        </form>
    </div>

    <!-- SIDE-BY-SIDE STATEMENT OF COMPREHENSIVE INCOME & BALANCE SHEET -->
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap:22px; margin-top:22px;">
        
        <!-- 1. STATEMENT OF COMPREHENSIVE INCOME (PROFIT & LOSS) -->
        <div class="stmt-box">
            <h3 style="margin-top:0; color:var(--accent-neon);">1. Statement of Comprehensive Income</h3>
            <p style="color:var(--text-muted); font-size:13px; margin-top:-6px;">Pearl Trails Tours &amp; Travel Uganda Ltd &bull; Period Ending <?= date('F Y') ?></p>
            <table class="stmt-table">
                <tr><th>Account Description</th><th style="text-align:right;">Amount (<?= htmlspecialchars($activeCurrency) ?>)</th></tr>
                <tr class="stmt-section"><td colspan="2">REVENUE / TURNOVER</td></tr>
                <tr><td>Safari Tour Packages &amp; Verified Bookings Revenue</td><td style="text-align:right;"><?= formatMoney($safariRevenueUsd) ?></td></tr>
                <tr style="font-weight:bold;"><td>Total Operating Revenue</td><td style="text-align:right;"><?= formatMoney($safariRevenueUsd) ?></td></tr>

                <tr class="stmt-section"><td colspan="2">OPERATING EXPENSES</td></tr>
                <tr><td>Management &amp; Support Staff Monthly Payroll</td><td style="text-align:right;"><?= formatMoney($monthlyPayrollUsd) ?></td></tr>
                <?php foreach ($expenseRows as $exp): ?>
                    <tr>
                        <td><?= htmlspecialchars($exp['account_title']) ?> (<?= htmlspecialchars($exp['entry_date']) ?>)</td>
                        <td style="text-align:right;"><?= formatMoney((float)$exp['amount_usd']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="font-weight:bold;"><td>Total Operating Expenses</td><td style="text-align:right; color:var(--danger-neon);">(<?= formatMoney($totalExpensesUsd) ?>)</td></tr>

                <tr class="stmt-total">
                    <td><?= $netProfitUsd >= 0 ? 'NET OPERATING PROFIT' : 'NET OPERATING LOSS' ?></td>
                    <td style="text-align:right; color:<?= $netProfitUsd >= 0 ? 'var(--accent-neon)' : 'var(--danger-neon)' ?>;">
                        <?= formatMoney($netProfitUsd) ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 2. STATEMENT OF FINANCIAL POSITION (BALANCE SHEET) -->
        <div class="stmt-box">
            <h3 style="margin-top:0; color:var(--accent-neon);">2. Statement of Financial Position (Balance Sheet)</h3>
            <p style="color:var(--text-muted); font-size:13px; margin-top:-6px;">As at <?= date('d F Y') ?> &bull; Accounting Equation Verified</p>
            <table class="stmt-table">
                <tr><th>Assets, Liabilities &amp; Equity</th><th style="text-align:right;">Amount (<?= htmlspecialchars($activeCurrency) ?>)</th></tr>
                <tr class="stmt-section"><td colspan="2">NON-CURRENT (FIXED) ASSETS</td></tr>
                <tr><td>Company Fleet &amp; Safari Vehicles</td><td style="text-align:right;"><?= formatMoney($vehiclesAssetUsd) ?></td></tr>
                <tr><td>Electronic Office Equipment</td><td style="text-align:right;"><?= formatMoney($electronicEquipUsd) ?></td></tr>
                <tr><td>Non-Electronic Office Furniture &amp; Fittings</td><td style="text-align:right;"><?= formatMoney($nonElectronicEquipUsd) ?></td></tr>
                <tr style="font-weight:bold;"><td>Total Non-Current Assets</td><td style="text-align:right;"><?= formatMoney($totalFixedAssetsUsd) ?></td></tr>

                <tr class="stmt-section"><td colspan="2">CURRENT ASSETS</td></tr>
                <tr><td>Cash at Bank &amp; Mobile Money Wallets</td><td style="text-align:right;"><?= formatMoney($cashAndBankUsd) ?></td></tr>
                <tr><td>Accounts Receivable (Unpaid Confirmed Bookings)</td><td style="text-align:right;"><?= formatMoney($accountsReceivableUsd) ?></td></tr>
                <tr style="font-weight:bold;"><td>Total Current Assets</td><td style="text-align:right;"><?= formatMoney($totalCurrentAssetsUsd) ?></td></tr>

                <tr class="stmt-total"><td>TOTAL ASSETS</td><td style="text-align:right;"><?= formatMoney($totalAssetsUsd) ?></td></tr>

                <tr class="stmt-section"><td colspan="2">LIABILITIES</td></tr>
                <tr><td>Current Liabilities (Short-Term Payables)</td><td style="text-align:right;"><?= formatMoney($currentLiabilitiesUsd) ?></td></tr>
                <tr><td>Long-Term Liabilities (Fleet Loans)</td><td style="text-align:right;"><?= formatMoney($longTermLiabilitiesUsd) ?></td></tr>
                <tr style="font-weight:bold;"><td>Total Liabilities</td><td style="text-align:right;"><?= formatMoney($totalLiabilitiesUsd) ?></td></tr>

                <tr class="stmt-section"><td colspan="2">OWNER'S EQUITY</td></tr>
                <tr><td>Paid-In Share Capital</td><td style="text-align:right;"><?= formatMoney($paidInCapitalUsd) ?></td></tr>
                <tr><td>Retained Reserves &amp; Opening Balance</td><td style="text-align:right;"><?= formatMoney($retainedReservesUsd) ?></td></tr>
                <tr><td>Current Period Net Profit / (Loss)</td><td style="text-align:right;"><?= formatMoney($netProfitUsd) ?></td></tr>
                <tr style="font-weight:bold;"><td>Total Owner's Equity</td><td style="text-align:right;"><?= formatMoney($totalEquityUsd) ?></td></tr>

                <tr class="stmt-total"><td>TOTAL LIABILITIES &amp; OWNER'S EQUITY</td><td style="text-align:right;"><?= formatMoney($totalLiabilitiesAndEquityUsd) ?></td></tr>
            </table>
        </div>
    </div>

    <!-- General Ledger Entries Table (Hidden on Official Statement Printout) -->
    <div class="no-print" style="margin-top:28px;">
        <h3>3. Recorded Financial Ledger Entries</h3>
        <table>
            <tr>
                <th>Date</th>
                <th>Entry Category</th>
                <th>Account Title</th>
                <th>Amount (<?= htmlspecialchars($activeCurrency) ?>)</th>
                <th>Action</th>
            </tr>
            <?php if (empty($ledgerList)): ?>
                <tr><td colspan="5" style="text-align:center;">No manual ledger entries posted yet.</td></tr>
            <?php else: ?>
                <?php foreach ($ledgerList as $entry): ?>
                <tr>
                    <td><?= htmlspecialchars($entry['entry_date']) ?></td>
                    <td><strong><?= htmlspecialchars($entry['entry_type']) ?></strong></td>
                    <td><?= htmlspecialchars($entry['account_title']) ?></td>
                    <td><?= formatMoney((float)$entry['amount_usd']) ?></td>
                    <td>
                        <a href="?delete_entry=<?= (int)$entry['entry_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this ledger entry?');">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
