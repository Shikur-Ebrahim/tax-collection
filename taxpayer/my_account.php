<?php
require_once '../config/auth.php';
$auth->requireRole('taxpayer');
$pageTitle = 'My Profile';
$sidebarRole = 'taxpayer';
require_once '../includes/header.php';

$db = Database::getInstance();
$userId = $auth->getUserId();
$taxpayer = $db->fetchOne('SELECT * FROM taxpayers WHERE user_id = ?', [$userId]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    
    $db->update('taxpayers', ['phone' => $phone, 'email' => $email], 'id = ?', [$taxpayer['id']]);
    $db->update('users', ['phone' => $phone, 'email' => $email], 'id = ?', [$userId]);
    
    $taxpayer = $db->fetchOne('SELECT * FROM taxpayers WHERE user_id = ?', [$userId]); // Refresh
    echo "<div class='alert alert-success' data-dismiss><span class='alert-icon'>✅</span> Profile updated successfully.</div>";
}
?>

<div class="form-grid cols-2">
    <!-- Personal Information Card -->
    <div class="card">
        <div class="card-header">
            <h3>Personal Information</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label text-muted">Full Name</label>
                <div class="fw-bold"><?= htmlspecialchars($taxpayer['full_name'] ?? 'N/A') ?></div>
            </div>
            <div class="form-group">
                <label class="form-label text-muted">Taxpayer Identification Number (TIN)</label>
                <div class="fw-bold text-primary" style="font-size: 1.2rem;"><?= htmlspecialchars($taxpayer['tin'] ?? 'N/A') ?></div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label text-muted">Gender</label>
                    <div><?= htmlspecialchars($taxpayer['gender'] ?? 'N/A') ?></div>
                </div>
                <div class="form-group">
                    <label class="form-label text-muted">Date of Birth</label>
                    <div><?= htmlspecialchars($taxpayer['date_of_birth'] ?? 'N/A') ?></div>
                </div>
            </div>
            <hr class="mb-16">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($taxpayer['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($taxpayer['email'] ?? '') ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary mt-8">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Business Information Card -->
    <div class="card">
        <div class="card-header">
            <h3>Business & Registration Information</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label text-muted">Registration Status</label>
                <div>
                    <span class="badge badge-<?= $taxpayer['registration_status'] == 'approved' ? 'success' : ($taxpayer['registration_status'] == 'pending' ? 'warning' : 'danger') ?>">
                        <?= strtoupper($taxpayer['registration_status'] ?? 'UNKNOWN') ?>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label text-muted">Taxpayer Type</label>
                <div><?= htmlspecialchars($taxpayer['taxpayer_type'] ?? 'N/A') ?></div>
            </div>
            
            <?php if (in_array($taxpayer['taxpayer_type'] ?? '', ['Business', 'Importer', 'Exporter'])): ?>
                <hr class="mb-16">
                <div class="form-group">
                    <label class="form-label text-muted">Business Name</label>
                    <div class="fw-bold"><?= htmlspecialchars($taxpayer['business_name'] ?? 'N/A') ?></div>
                </div>
                <div class="form-group">
                    <label class="form-label text-muted">Business Type</label>
                    <div><?= htmlspecialchars($taxpayer['business_type'] ?? 'N/A') ?></div>
                </div>
                <div class="form-group">
                    <label class="form-label text-muted">Trade License Number</label>
                    <div><?= htmlspecialchars($taxpayer['trade_license_no'] ?? 'N/A') ?> (Expires: <?= htmlspecialchars($taxpayer['trade_license_expiry'] ?? 'N/A') ?>)</div>
                </div>
                <div class="form-group">
                    <label class="form-label text-muted">Business Address</label>
                    <div><?= htmlspecialchars($taxpayer['business_address'] ?? 'N/A') ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
