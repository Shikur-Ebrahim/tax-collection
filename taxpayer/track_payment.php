<?php
require_once '../config/auth.php';
$auth->requireRole('taxpayer');
$pageTitle = 'Track Payment';
$sidebarRole = 'taxpayer';
require_once '../includes/header.php';

$db = Database::getInstance();
$userId = $auth->getUserId();
$taxpayer = $db->fetchOne('SELECT * FROM taxpayers WHERE user_id = ?', [$userId]);

$searchResult = null;
if (isset($_GET['ref']) && !empty($_GET['ref'])) {
    $ref = $_GET['ref'];
    $searchResult = $db->fetchOne("
        SELECT p.*, t.tax_year, t.tax_period 
        FROM payments p 
        JOIN tax_records t ON p.tax_record_id = t.id 
        WHERE p.payment_reference = ? AND p.taxpayer_id = ?
    ", [$ref, $taxpayer['id'] ?? 0]);
}
?>

<div class="card mb-24">
    <div class="card-body text-center" style="padding: 40px 20px;">
        <h2 class="mb-16 text-primary">Track Your Payment</h2>
        <p class="text-muted mb-24">Enter your payment reference number to check its verification status.</p>
        <form method="GET" class="d-flex justify-center gap-12" style="max-width: 500px; margin: 0 auto;">
            <input type="text" name="ref" class="form-control" placeholder="e.g. PAY-1698765432-123" value="<?= htmlspecialchars($_GET['ref'] ?? '') ?>" required>
            <button type="submit" class="btn btn-primary">Track</button>
        </form>
    </div>
</div>

<?php if (isset($_GET['ref'])): ?>
    <?php if ($searchResult): ?>
        <div class="card">
            <div class="card-header">
                <h3>Payment Status Result</h3>
            </div>
            <div class="card-body">
                <div class="step-wizard">
                    <div class="step done">
                        <div class="step-circle">1</div>
                        <div class="step-label">Submitted<br><?= date('M j, Y', strtotime($searchResult['payment_date'])) ?></div>
                    </div>
                    <div class="step <?= $searchResult['status'] !== 'pending' ? 'done' : 'active' ?>">
                        <div class="step-circle">2</div>
                        <div class="step-label">Processing<br>Bank Verification</div>
                    </div>
                    <div class="step <?= $searchResult['status'] === 'verified' ? 'done' : ($searchResult['status'] === 'failed' ? 'error' : '') ?>">
                        <div class="step-circle"><?= $searchResult['status'] === 'failed' ? '❌' : '3' ?></div>
                        <div class="step-label">
                            <?= $searchResult['status'] === 'verified' ? 'Verified & Complete' : ($searchResult['status'] === 'failed' ? 'Verification Failed' : 'Receipt Issued') ?>
                            <?= $searchResult['verified_at'] ? '<br>' . date('M j, Y', strtotime($searchResult['verified_at'])) : '' ?>
                        </div>
                    </div>
                </div>

                <div class="form-grid cols-2 mt-24">
                    <div class="form-group">
                        <label class="form-label text-muted">Reference Number</label>
                        <div class="fw-bold"><?= htmlspecialchars($searchResult['payment_reference']) ?></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-muted">Tax Period</label>
                        <div class="fw-bold"><?= $searchResult['tax_year'] ?> (<?= $searchResult['tax_period'] ?>)</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-muted">Payment Method</label>
                        <div class="fw-bold"><?= htmlspecialchars($searchResult['payment_method']) ?></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-muted">Amount</label>
                        <div class="fw-bold text-primary">ETB <?= number_format($searchResult['amount_paid'], 2) ?></div>
                    </div>
                    <?php if ($searchResult['notes']): ?>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label text-muted">Bank Notes / Reason</label>
                            <div><?= htmlspecialchars($searchResult['notes']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <span class="alert-icon">❌</span> 
            <div>Payment reference not found or does not belong to your account. Please check the reference number and try again.</div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
