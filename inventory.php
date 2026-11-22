<?php
require 'db.php';
require_once 'currency_helper.php';

// STRICT ACCESS CONTROL: System Administrator Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$msg = '';

// 1. Add Office Equipment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_equipment'])) {
    $stmt = $pdo->prepare("INSERT INTO office_equipment (item_name, category, serial_or_tag, quantity, purchase_value_usd, condition_status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([trim($_POST['item_name']), $_POST['category'], trim($_POST['serial_or_tag']), (int)$_POST['quantity'], (float)$_POST['purchase_value_usd'], $_POST['condition_status']]);
    $msg = "Office equipment item recorded.";
}

// 2. Add Company Vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle'])) {
    $stmt = $pdo->prepare("INSERT INTO company_vehicles (plate_number, model_name, vehicle_category, seating_capacity, valuation_usd, operational_status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([strtoupper(trim($_POST['plate_number'])), trim($_POST['model_name']), $_POST['vehicle_category'], (int)$_POST['seating_capacity'], (float)$_POST['valuation_usd'], $_POST['operational_status']]);
    $msg = "Company vehicle added to fleet register.";
}

// 3. Add Office File
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_file'])) {
    $stmt = $pdo->prepare("INSERT INTO office_files (file_reference, file_title, department, cabinet_location, created_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([strtoupper(trim($_POST['file_reference'])), trim($_POST['file_title']), trim($_POST['department']), trim($_POST['cabinet_location']), $_POST['created_date']]);
    $msg = "Office file indexed successfully.";
}

// 4. Add Company Staff
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $stmt = $pdo->prepare("INSERT INTO company_staff (full_name, staff_category, job_title, phone_contact, monthly_salary_usd, employment_status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([trim($_POST['full_name']), $_POST['staff_category'], trim($_POST['job_title']), trim($_POST['phone_contact']), (float)$_POST['monthly_salary_usd'], $_POST['employment_status']]);
    $msg = "Employee added to staff register.";
}

// Delete handlers
if (isset($_GET['del_equip'])) { $pdo->prepare("DELETE FROM office_equipment WHERE equip_id=?")->execute([(int)$_GET['del_equip']]); $msg = "Equipment removed."; }
if (isset($_GET['del_veh']))   { $pdo->prepare("DELETE FROM company_vehicles WHERE vehicle_id=?")->execute([(int)$_GET['del_veh']]); $msg = "Vehicle removed."; }
if (isset($_GET['del_file']))  { $pdo->prepare("DELETE FROM office_files WHERE file_id=?")->execute([(int)$_GET['del_file']]); $msg = "File removed."; }
if (isset($_GET['del_staff'])) { $pdo->prepare("DELETE FROM company_staff WHERE staff_id=?")->execute([(int)$_GET['del_staff']]); $msg = "Staff record removed."; }

$equipment = $pdo->query("SELECT * FROM office_equipment ORDER BY category ASC, equip_id DESC")->fetchAll();
$vehicles  = $pdo->query("SELECT * FROM company_vehicles ORDER BY vehicle_category ASC, vehicle_id DESC")->fetchAll();
$files     = $pdo->query("SELECT * FROM office_files ORDER BY file_id DESC")->fetchAll();
$staff     = $pdo->query("SELECT * FROM company_staff ORDER BY staff_category ASC, staff_id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Inventory & Staff Register (Admin Only)</title>
    <link rel="stylesheet" href="style.css">
   <style>
    .sec-box { margin-top: 28px; padding-top: 12px; border-top: 1px solid var(--border-color); }
    .cat-pill { background: var(--accent-neon-soft); color: var(--accent-neon); padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }

    @media print {
        /* 1. Keep Top Land Cruiser Banner & Company Title as Official Letterhead */
        .top-safari-header-strip {
            display: flex !important;
            background: #f8f9fa !important;
            border: 2px solid #1b4332 !important;
            border-radius: 8px !important;
            margin-bottom: 16px !important;
            padding: 10px 16px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .brand-title-box {
            background: transparent !important;
            border-right: 2px solid #1b4332 !important;
        }
        .brand-title-box strong { color: #1b4332 !important; }
        .brand-title-box small { color: #8c6239 !important; }
        .savanna-runway {
            background: #eef6f1 !important;
            border: 1px solid #1b4332 !important;
        }
        .cruiser-hero-rig {
            animation: none !important;
            right: 24px !important;
        }

        /* 2. Keep Bottom Footer & Copyright Markers Visible */
        .site-footer {
            display: block !important;
            margin-top: 24px !important;
            padding-top: 10px !important;
            border-top: 2px solid #1b4332 !important;
            background: transparent !important;
            color: #111111 !important;
            text-align: center !important;
            font-size: 12px !important;
        }
        .site-footer p, .site-footer strong, .site-footer a {
            color: #111111 !important;
            text-decoration: none !important;
        }

        /* 3. Hide Navigation Menu, Buttons, Input Forms, Action Columns & Floating Widgets */
        header, nav, .no-print, button, .btn, form,
        #safariChatToggle, #safariChatBox, #settingsModal, #signLanguageBar,
        #adminAlertDrawer, #liveAlertToast {
            display: none !important;
        }

        /* 4. Clean White-Paper Table Styling */
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        table, th, td {
            border: 1px solid #444 !important;
            color: #000000 !important;
            background: #ffffff !important;
        }
        th {
            background: #e9ecef !important;
            font-weight: bold !important;
        }
        .cat-pill {
            border: 1px solid #333 !important;
            background: #f8f9fa !important;
            color: #000000 !important;
        }
    }
</style>
>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h2>📦 Company Assets, Fleet, Office Files & Staff Inventory</h2>
        <button onclick="window.print()" class="btn btn-edit no-print">Print Inventory</button>
    </div>
    <p style="color:var(--text-muted); margin-top:-6px;">Restricted Access: System Administrator Only</p>

    <?php if ($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <!-- 1. OFFICE EQUIPMENT (ELECTRONIC & NON-ELECTRONIC) -->
    <div class="sec-box">
        <h3>1. Office Equipment Register (Electronic & Non-Electronic)</h3>
        <form method="POST" class="grid no-print" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap:10px;">
            <input type="text" name="item_name" placeholder="Equipment Name" required>
            <select name="category"><option value="Electronic">Electronic</option><option value="Non-Electronic">Non-Electronic</option></select>
            <input type="text" name="serial_or_tag" placeholder="Asset Tag / Serial" required>
            <input type="number" name="quantity" min="1" value="1" placeholder="Qty" required>
            <input type="number" step="0.01" name="purchase_value_usd" placeholder="Total Value (USD)" required>
            <select name="condition_status"><option value="Good">Good</option><option value="Needs Repair">Needs Repair</option><option value="Retired">Retired</option></select>
            <button type="submit" name="add_equipment">Add Equipment</button>
        </form>
        <table>
            <tr><th>Asset Tag</th><th>Equipment Name</th><th>Category</th><th>Qty</th><th>Valuation (<?= htmlspecialchars($activeCurrency) ?>)</th><th>Condition</th><th class="no-print">Action</th></tr>
            <?php foreach ($equipment as $eq): ?>
            <tr>
                <td><code><?= htmlspecialchars($eq['serial_or_tag']) ?></code></td>
                <td><strong><?= htmlspecialchars($eq['item_name']) ?></strong></td>
                <td><span class="cat-pill"><?= htmlspecialchars($eq['category']) ?></span></td>
                <td><?= (int)$eq['quantity'] ?></td>
                <td><?= formatMoney((float)$eq['purchase_value_usd']) ?></td>
                <td><?= htmlspecialchars($eq['condition_status']) ?></td>
                <td class="no-print"><a href="?del_equip=<?= (int)$eq['equip_id'] ?>" class="btn btn-danger" style="padding:4px 8px; font-size:12px;">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- 2. COMPANY VEHICLES (SALOON, WAGON, PICKUP, SURV, LORRY) -->
    <div class="sec-box">
        <h3>2. Company Fleet & Vehicles (Saloon, Wagon, Pickup, SURV, Lorry)</h3>
        <form method="POST" class="grid no-print" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap:10px;">
            <input type="text" name="plate_number" placeholder="Plate No. (e.g. UBG 123A)" required>
            <input type="text" name="model_name" placeholder="Vehicle Model" required>
            <select name="vehicle_category">
                <option value="Saloon">Saloon</option>
                <option value="Wagon">Wagon</option>
                <option value="Pickup">Pickup</option>
                <option value="SURV">SURV</option>
                <option value="Lorry">Lorry</option>
            </select>
            <input type="number" name="seating_capacity" min="2" value="5" placeholder="Seats" required>
            <input type="number" step="0.01" name="valuation_usd" placeholder="Valuation (USD)" required>
            <select name="operational_status"><option value="Available">Available</option><option value="On Safari">On Safari</option><option value="In Garage">In Garage</option></select>
            <button type="submit" name="add_vehicle">Add Vehicle</button>
        </form>
        <table>
            <tr><th>Plate No.</th><th>Vehicle Model</th><th>Category</th><th>Seats</th><th>Valuation (<?= htmlspecialchars($activeCurrency) ?>)</th><th>Status</th><th class="no-print">Action</th></tr>
            <?php foreach ($vehicles as $v): ?>
            <tr>
                <td><strong><?= htmlspecialchars($v['plate_number']) ?></strong></td>
                <td><?= htmlspecialchars($v['model_name']) ?></td>
                <td><span class="cat-pill"><?= htmlspecialchars($v['vehicle_category']) ?></span></td>
                <td><?= (int)$v['seating_capacity'] ?></td>
                <td><?= formatMoney((float)$v['valuation_usd']) ?></td>
                <td><?= htmlspecialchars($v['operational_status']) ?></td>
                <td class="no-print"><a href="?del_veh=<?= (int)$v['vehicle_id'] ?>" class="btn btn-danger" style="padding:4px 8px; font-size:12px;">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- 3. OFFICE FILES & ARCHIVES -->
    <div class="sec-box">
        <h3>3. Office Files & Archival Registry</h3>
        <form method="POST" class="grid no-print" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px;">
            <input type="text" name="file_reference" placeholder="File Ref (e.g. PTU/OPS/04)" required>
            <input type="text" name="file_title" placeholder="File Title / Subject" required>
            <input type="text" name="department" placeholder="Department" required>
            <input type="text" name="cabinet_location" placeholder="Cabinet / Shelf Location" required>
            <input type="date" name="created_date" value="<?= date('Y-m-d') ?>" required>
            <button type="submit" name="add_file">Index File</button>
        </form>
        <table>
            <tr><th>File Reference</th><th>File Title</th><th>Department</th><th>Cabinet Location</th><th>Date Opened</th><th class="no-print">Action</th></tr>
            <?php foreach ($files as $f): ?>
            <tr>
                <td><code><?= htmlspecialchars($f['file_reference']) ?></code></td>
                <td><strong><?= htmlspecialchars($f['file_title']) ?></strong></td>
                <td><?= htmlspecialchars($f['department']) ?></td>
                <td><?= htmlspecialchars($f['cabinet_location']) ?></td>
                <td><?= htmlspecialchars($f['created_date']) ?></td>
                <td class="no-print"><a href="?del_file=<?= (int)$f['file_id'] ?>" class="btn btn-danger" style="padding:4px 8px; font-size:12px;">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="sec-box">
        <h3>4. Company Staff Directory (Management & Support Staff)</h3>
        <form method="POST" class="grid no-print" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap:10px;">
            <input type="text" name="full_name" placeholder="Employee Full Name" required>
            <select name="staff_category"><option value="Management">Management</option><option value="Support Staff">Support Staff</option></select>
            <input type="text" name="job_title" placeholder="Job Title / Position" required>
            <input type="text" name="phone_contact" placeholder="Phone Contact" required>
            <input type="number" step="0.01" name="monthly_salary_usd" placeholder="Monthly Salary (USD)" required>
            <select name="employment_status"><option value="Active">Active</option><option value="On Leave">On Leave</option><option value="Terminated">Terminated</option></select>
            <button type="submit" name="add_staff">Add Employee</button>
        </form>
        <table>
            <tr><th>Staff ID</th><th>Employee Name</th><th>Staff Category</th><th>Job Title</th><th>Contact</th><th>Monthly Salary (<?= htmlspecialchars($activeCurrency) ?>)</th><th>Status</th><th class="no-print">Action</th></tr>
            <?php foreach ($staff as $st): ?>
            <tr>
                <td>#<?= (int)$st['staff_id'] ?></td>
                <td><strong><?= htmlspecialchars($st['full_name']) ?></strong></td>
                <td><span class="cat-pill"><?= htmlspecialchars($st['staff_category']) ?></span></td>
                <td><?= htmlspecialchars($st['job_title']) ?></td>
                <td><?= htmlspecialchars($st['phone_contact']) ?></td>
                <td><?= formatMoney((float)$st['monthly_salary_usd']) ?></td>
                <td><?= htmlspecialchars($st['employment_status']) ?></td>
                <td class="no-print"><a href="?del_staff=<?= (int)$st['staff_id'] ?>" class="btn btn-danger" style="padding:4px 8px; font-size:12px;">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
