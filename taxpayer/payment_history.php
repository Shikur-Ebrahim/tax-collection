<?php
require_once '../config/auth.php';
$auth->requireRole('taxpayer');
$pageTitle = 'Payment History';
$sidebarRole = 'taxpayer';
require_once '../includes/header.php';

$db = Database::getInstance();
$userId = $auth->getUserId();
$taxpayer = $db->fetchOne('SELECT * FROM taxpayers WHERE user_id = ?', [$userId]);

if (!$taxpayer) {
    echo "<div class='alert alert-warning'><span class='alert-icon'>⚠️</span> <div><strong>Not Registered</strong></div></div>";
    require_once '../includes/footer.php';
    exit;
}

$payments = $db->fetchAll("
    SELECT p.*, t.tax_year, t.tax_period 
    FROM payments p 
    JOIN tax_records t ON p.tax_record_id = t.id 
    WHERE p.taxpayer_id = ? 
    ORDER BY p.payment_date DESC
", [$taxpayer['id']]);
?>

<div class="card">
    <div class="card-header">
        <h3>My Payment History</h3>
        <input type="text" class="form-control" style="width: 250px;" placeholder="Search references..." data-search-for="payments-table">
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table" id="payments-table">
                <thead>
                    <tr>
                        <th>Reference No.</th>
                        <th>Tax Year</th>
                        <th>Method</th>
                        <th>Amount Paid</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="7" class="text-center text-muted">No payments found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($payment['payment_reference']) ?></td>
                                <td><?= $payment['tax_year'] ?> (<?= $payment['tax_period'] ?>)</td>
                                <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                                <td class="text-primary fw-bold">ETB <?= number_format($payment['amount_paid'], 2) ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($payment['payment_date'])) ?></td>
                                <td>
                                    <?php 
                                    $badge = 'warning';
                                    if ($payment['status'] === 'verified') $badge = 'success';
                                    if ($payment['status'] === 'failed') $badge = 'danger';
                                    ?>
                                    <span class="badge badge-<?= $badge ?>"><?= ucfirst($payment['status']) ?></span>
                                </td>
                                <td>
                                    <?php if ($payment['status'] === 'verified'): ?>
                                        <a href="receipts.php" class="btn btn-sm btn-outline-primary">View Receipt</a>
                                    <?php else: ?>
                                        <span class="text-muted text-sm">Pending Verification</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
