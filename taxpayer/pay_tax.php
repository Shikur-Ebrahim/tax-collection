<?php
require_once '../config/auth.php';
$auth->requireRole('taxpayer');
$pageTitle = 'Pay Tax';
$sidebarRole = 'taxpayer';
require_once '../includes/header.php';

$db = Database::getInstance();
$userId = $auth->getUserId();
$taxpayer = $db->fetchOne('SELECT * FROM taxpayers WHERE user_id = ?', [$userId]);

if (!$taxpayer || $taxpayer['registration_status'] !== 'approved') {
    echo "<div class='alert alert-danger'><span class='alert-icon'>❌</span> <div><strong>Cannot Pay Tax</strong><br>Your registration is not yet approved.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    $recordId = $_POST['tax_record_id'];
    $method = $_POST['payment_method'];
    $bankRef = $_POST['bank_transaction_ref'];
    
    $record = $db->fetchOne("SELECT * FROM tax_records WHERE id = ? AND taxpayer_id = ?", [$recordId, $taxpayer['id']]);
    
    if ($record && $record['status'] !== 'paid') {
        $paymentRef = 'PAY-' . time() . '-' . rand(100, 999);
        $amount = $record['total_due'];
        
        $db->insert('payments', [
            'tax_record_id' => $record['id'],
            'taxpayer_id' => $taxpayer['id'],
            'payment_reference' => $paymentRef,
            'payment_method' => $method,
            'amount_paid' => $amount,
            'bank_transaction_ref' => $bankRef,
            'status' => 'pending'
        ]);
        
        $db->update('tax_records', ['status' => 'pending_payment'], 'id = ?', [$record['id']]);
        
        echo "<div class='alert alert-success'><span class='alert-icon'>✅</span> <div><strong>Payment Submitted Successfully!</strong><br>Your payment reference is <strong>{$paymentRef}</strong>. The bank will verify this payment shortly.</div></div>";
    }
}

// Fetch pending records
$pendingRecords = $db->fetchAll("SELECT * FROM tax_records WHERE taxpayer_id = ? AND status IN ('assessed', 'overdue')", [$taxpayer['id']]);
?>

<div class="card">
    <div class="card-header">
        <h3>Submit Tax Payment</h3>
    </div>
    <div class="card-body">
        <?php if (empty($pendingRecords)): ?>
            <div class="empty-state">
                <div class="empty-icon">🎉</div>
                <h3>All caught up!</h3>
                <p>You have no outstanding tax assessments to pay.</p>
            </div>
        <?php else: ?>
            <form method="POST" data-validate>
                <div class="form-group mb-24">
                    <label class="form-label">Select Tax Assessment to Pay <span class="req">*</span></label>
                    <?php foreach ($pendingRecords as $record): ?>
                        <div class="card mb-12" style="border: 2px solid var(--border);">
                            <label class="card-body d-flex align-center gap-16" style="cursor: pointer; padding: 16px;">
                                <input type="radio" name="tax_record_id" value="<?= $record['id'] ?>" required>
                                <div>
                                    <div class="fw-bold">Tax Year: <?= $record['tax_year'] ?> (<?= $record['tax_period'] ?>)</div>
                                    <div class="text-muted text-sm">Assessed Income: ETB <?= number_format($record['income_amount'], 2) ?></div>
                                </div>
                                <div style="margin-left: auto; text-align: right;">
                                    <div class="text-primary fw-bold" style="font-size: 1.2rem;">ETB <?= number_format($record['total_due'], 2) ?></div>
                                    <span class="badge badge-<?= $record['status'] == 'overdue' ? 'danger' : 'warning' ?>"><?= strtoupper($record['status']) ?></span>
                                </div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-group mb-24">
                    <label class="form-label">Payment Method <span class="req">*</span></label>
                    <select name="payment_method" class="form-control" required>
                        <option value="">Select Method...</option>
                        <option value="CBE">Commercial Bank of Ethiopia (CBE)</option>
                        <option value="Telebirr">Telebirr</option>
                        <option value="BankTransfer">Bank Transfer</option>
                        <option value="Cash">Cash at Office</option>
                    </select>
                </div>

                <div class="form-group mb-24">
                    <label class="form-label">Bank Transaction Reference Number</label>
                    <input type="text" name="bank_transaction_ref" class="form-control" placeholder="Enter transaction reference from your bank receipt">
                    <div class="form-text">Required for CBE, Telebirr, and Bank Transfers to verify your payment faster.</div>
                </div>

                <button type="submit" name="submit_payment" class="btn btn-primary btn-lg">Submit Payment for Verification</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
