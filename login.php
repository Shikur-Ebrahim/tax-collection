<?php
require_once 'config/auth.php';

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: ' . Auth::getDashboardUrl($auth->getRole()));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = $auth->login($username, $password);
    if ($result['success']) {
        header('Location: ' . Auth::getDashboardUrl($result['role']));
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bure Town TCMS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-left">
                <div>
                    <div class="brand">
                        <div class="icon">🏛️</div>
                        <div>
                            <h1>Bure Town TCMS</h1>
                            <p>Tax Collection Management System</p>
                        </div>
                    </div>
                    <div class="hero">
                        <h2>Welcome Back!</h2>
                        <p>Sign in to access your dashboard, manage tax records, and process payments securely.</p>
                    </div>
                    <ul class="features">
                        <li>Secure Role-based Access</li>
                        <li>Real-time Payment Tracking</li>
                        <li>Automated Tax Calculation</li>
                        <li>Instant Receipt Generation</li>
                    </ul>
                </div>
                <footer>
                    &copy; <?= date('Y') ?> Bure Town Administration.
                </footer>
            </div>
            <div class="login-right">
                <h2>Sign In</h2>
                <p>Enter your credentials to access the system.</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><span class="alert-icon">⚠️</span> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out'): ?>
                    <div class="alert alert-success"><span class="alert-icon">✅</span> You have been logged out successfully.</div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Username or Email</label>
                        <input type="text" name="username" class="form-control" required placeholder="admin">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg" style="justify-content: center; margin-top: 10px;">Log In</button>
                </form>
                
                <div style="text-align: center; margin-top: 24px; font-size: 0.85rem;">
                    Don't have an account? <a href="register.php" class="fw-bold">Register here</a>
                </div>

                <div class="demo-creds">
                    <h4>Demo Accounts (Password: Admin@1234)</h4>
                    <table>
                        <tr><td>Admin:</td><td>admin</td></tr>
                        <tr><td>Staff:</td><td>staffworker</td></tr>
                        <tr><td>Officer:</td><td>taxofficer</td></tr>
                        <tr><td>Bank:</td><td>bankprovider</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
