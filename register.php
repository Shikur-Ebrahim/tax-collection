<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    $taxpayerType = $_POST['taxpayer_type'];
    $businessName = trim($_POST['business_name']);
    $businessAddress = trim($_POST['business_address']);
    
    if ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif ($db->count('users', 'username = ? OR email = ?', [$username, $email]) > 0) {
        $error = 'Username or email already exists.';
    } else {
        try {
            $db->getConnection()->beginTransaction();
            
            // Create user
            $userId = $db->insert('users', [
                'full_name' => $fullName,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => password_hash($password, HASH_ALGO, ['cost' => HASH_COST]),
                'role_id' => 4, // taxpayer
                'status' => 'active'
            ]);
            
            // Generate TIN (BUR + Year + 4 digit ID)
            $lastId = $db->fetchOne("SELECT MAX(id) as max_id FROM taxpayers")['max_id'] ?? 0;
            $tin = 'BUR' . date('Y') . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            
            // Create taxpayer
            $db->insert('taxpayers', [
                'user_id' => $userId,
                'tin' => $tin,
                'full_name' => $fullName,
                'gender' => $gender,
                'date_of_birth' => $dob,
                'phone' => $phone,
                'email' => $email,
                'taxpayer_type' => $taxpayerType,
                'business_name' => $businessName,
                'business_address' => $businessAddress,
                'registration_status' => 'pending'
            ]);
            
            $db->getConnection()->commit();
            header('Location: login.php?msg=registered');
            exit;
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Taxpayer Registration - Bure Town TCMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f4f7f6; display: flex; justify-content: center; padding: 40px 20px; }
        .reg-container { background: #fff; width: 100%; max-width: 800px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 40px; }
        .reg-header { text-align: center; margin-bottom: 30px; }
        .reg-header h2 { color: var(--primary-dark); }
    </style>
</head>
<body>
    <div class="reg-container">
        <div class="reg-header">
            <h2>🏛️ Taxpayer Registration</h2>
            <p class="text-muted">Create your account to manage your taxes online.</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <h3 class="mb-16">Personal Details</h3>
            <div class="form-grid cols-2">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-control" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="date_of_birth" class="form-control" required value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Taxpayer Type *</label>
                    <select name="taxpayer_type" id="tax_type" class="form-control" required onchange="toggleBiz()">
                        <option value="Employed">Employed</option>
                        <option value="Business">Business Owner</option>
                        <option value="Investor">Investor</option>
                    </select>
                </div>
            </div>
            
            <div id="biz-section" style="display:none;">
                <h3 class="mb-16 mt-24">Business Details</h3>
                <div class="form-grid cols-2">
                    <div class="form-group">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="business_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Business Address</label>
                        <input type="text" name="business_address" class="form-control">
                    </div>
                </div>
            </div>
            
            <h3 class="mb-16 mt-24">Account Security</h3>
            <div class="form-grid cols-2">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group mt-24">
                <label style="display: flex; gap: 10px; align-items: center;">
                    <input type="checkbox" required> I agree to provide accurate information and accept the terms.
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg w-100" style="justify-content: center; margin-top: 20px;">Register Account</button>
            <div style="text-align: center; margin-top: 20px;">
                Already have an account? <a href="login.php">Log in</a>
            </div>
        </form>
    </div>
    
    <script>
        function toggleBiz() {
            const val = document.getElementById('tax_type').value;
            document.getElementById('biz-section').style.display = (val === 'Business' || val === 'Investor') ? 'block' : 'none';
        }
    </script>
</body>
</html>
