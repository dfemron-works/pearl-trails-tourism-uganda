<?php
require 'db.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName      = trim($_POST['full_name']);
    $dob           = $_POST['dob'];
    $nationality   = trim($_POST['nationality']);
    $idNumber      = strtoupper(trim($_POST['id_number']));
    $gender        = $_POST['gender'];
    $maritalStatus = trim($_POST['marital_status'] ?? '');
    $email         = trim($_POST['email']);
    $mobileContact = trim($_POST['mobile_contact']);
    $officeLine    = trim($_POST['office_line'] ?? '');
    $designation   = trim($_POST['designation']);
    $pass          = $_POST['password'];

    if (strlen($pass) < 6) {
        $msg = "Error: Password must be at least 6 characters.";
    } elseif ($dob >= date('Y-m-d')) {
        $msg = "Error: Date of birth must be in the past.";
    } elseif ($idNumber === '') {
        $msg = "Error: Please enter a valid NIN or Passport Number.";
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("
                INSERT INTO users (
                    full_name, dob, nationality, id_number, gender, marital_status,
                    email, mobile_contact, office_line, designation, password_hash, role
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'tourist')
            ");
            $stmt->execute([
                $fullName,
                $dob,
                $nationality,
                $idNumber,
                $gender,
                $maritalStatus !== '' ? $maritalStatus : null,
                $email,
                $mobileContact,
                $officeLine !== '' ? $officeLine : null,
                $designation,
                $hash
            ]);

            // Trigger Real-Time Admin Alert for New Account Creation
            triggerAdminAlert(
                $pdo,
                'NEW_ACCOUNT',
                'INFO',
                '🆕 New Account Registered',
                "$fullName ($nationality, $email) opened a new client account.",
                'Registration Form'
            );

            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $msg = "Error: Email address is already registered.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Account - Pearl Trails Uganda</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container" style="max-width: 760px;">
    <h2>Tourist & Client Registration</h2>
    <p style="color: var(--text-muted);">Complete your profile below to book safaris and manage itineraries.</p>

    <?php if ($msg): ?>
        <div class="alert error"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            <div>
                <label>Full Names *</label>
                <input type="text" name="full_name" placeholder="e.g., Akello Grace" required>
            </div>

            <div>
                <label>Date of Birth *</label>
                <input type="date" name="dob" max="<?= date('Y-m-d', strtotime('-16 years')) ?>" required>
            </div>

            <div>
                <label>Nationality *</label>
                <input type="text" name="nationality" value="Ugandan" required>
            </div>

            <div>
                <label>NIN Number or Passport Number *</label>
                <input type="text" name="id_number" placeholder="e.g., CM95012345678A or B1234567" required>
            </div>

            <div>
                <label>Gender *</label>
                <select name="gender" required>
                    <option value="">-- Select Gender --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Prefer not to say">Prefer not to say</option>
                </select>
            </div>

            <div>
                <label>Marital Status (Optional)</label>
                <select name="marital_status">
                    <option value="">-- Optional --</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Divorced">Divorced</option>
                    <option value="Widowed">Widowed</option>
                </select>
            </div>

            <div>
                <label>Email Address *</label>
                <input type="email" name="email" placeholder="name@example.com" required>
            </div>

            <div>
                <label>Mobile Contact *</label>
                <input type="tel" name="mobile_contact" placeholder="e.g., +256 772 123456" required>
            </div>

            <div>
                <label>Office Line (Optional)</label>
                <input type="tel" name="office_line" placeholder="e.g., +256 414 123456">
            </div>

            <div>
                <label>Designation / Occupation *</label>
                <input type="text" name="designation" placeholder="e.g., Software Engineer / Lecturer / Tourist" required>
            </div>
        </div>

        <label>Create Password (minimum 6 characters) *</label>
        <input type="password" name="password" minlength="6" required>

        <button type="submit" style="width:100%; margin-top:8px;">Complete Registration</button>
    </form>
</div>
</body>
</html>

