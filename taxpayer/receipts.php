<?php
require_once __DIR__ . '/../config/auth.php';
$auth->requireRole('taxpayer');
$pageTitle = 'My Tax Receipts';
$sidebarRole = 'taxpayer';
require_once __DIR__ . '/../includes/header.php';

$db = Database::getInstance();
$userId = $auth->getUserId();
$taxpayer = $db->fetchOne('SELECT * FROM taxpayers WHERE user_id = ?', [$userId]);

if (!$taxpayer) {
    echo "<div class='alert alert-warning'>Not registered.</div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$receipts = $db->fetchAll("
    SELECT r.*, p.payment_method, t.tax_year, t.tax_period, u.full_name as issued_by_name
    FROM receipts r
    JOIN payments p ON r.payment_id = p.id
    JOIN tax_records t ON r.tax_record_id = t.id
    LEFT JOIN users u ON r.issued_by = u.id
    WHERE r.taxpayer_id = ?
    ORDER BY r.issued_date DESC
", [$taxpayer['id']]);
?>

<div class="card">
    <div class="card-header">
        <h3>Official Tax Receipts</h3>
    </div>
    <div class="card-body">
        <?php if (empty($receipts)): ?>
            <div class="empty-state">
                <div class="empty-icon">🧾</div>
                <h3>No Receipts Found</h3>
                <p>You don't have any verified tax payments yet.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Receipt Number</th>
                            <th>Tax Year</th>
                            <th>Date Issued</th>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receipts as $receipt): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($receipt['receipt_number']) ?></td>
                                <td><?= $receipt['tax_year'] ?> (<?= $receipt['tax_period'] ?>)</td>
                                <td><?= date('M j, Y', strtotime($receipt['issued_date'])) ?></td>
                                <td><?= htmlspecialchars($receipt['payment_method']) ?></td>
                                <td class="text-success fw-bold">ETB <?= number_format($receipt['amount_paid'], 2) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-modal="receipt-modal-<?= $receipt['id'] ?>">View</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php foreach ($receipts as $receipt): ?>
<div class="modal-overlay" id="receipt-modal-<?= $receipt['id'] ?>">
    <div class="modal" style="width: 600px;">
        <div class="modal-header no-print">
            <h3>Receipt Detail</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="print-area-<?= $receipt['id'] ?>">
            <div class="receipt">
                <div class="receipt-header">
                    <h2>🏛 Bure Town TCMS</h2>
                    <p>Official Tax Payment Receipt</p>
                </div>
                <div class="receipt-stamp">PAID</div>
                <table class="receipt-table">
                    <tr>
                        <td>Receipt Number:</td>
                        <td><?= htmlspecialchars($receipt['receipt_number']) ?></td>
                    </tr>
                    <tr>
                        <td>Date Issued:</td>
                        <td><?= date('d/m/Y H:i:s', strtotime($receipt['issued_date'])) ?></td>
                    </tr>
                    <tr>
                        <td>Taxpayer Name:</td>
                        <td><?= htmlspecialchars($taxpayer['full_name']) ?></td>
                    </tr>
                    <tr>
                        <td>TIN Number:</td>
                        <td><?= htmlspecialchars($taxpayer['tin']) ?></td>
                    </tr>
                    <tr>
                        <td>Tax Year / Period:</td>
                        <td><?= $receipt['tax_year'] ?> (<?= $receipt['tax_period'] ?>)</td>
                    </tr>
                    <tr>
                        <td>Payment Method:</td>
                        <td><?= htmlspecialchars($receipt['payment_method']) ?></td>
                    </tr>
                    <tr>
                        <td>Verified By:</td>
                        <td><?= htmlspecialchars($receipt['issued_by_name'] ?? 'System') ?></td>
                    </tr>
                </table>
                <div class="receipt-total">
                    <span>Total Amount Paid:</span>
                    <span class="amount">ETB <?= number_format($receipt['amount_paid'], 2) ?></span>
                </div>
                <div class="receipt-footer">
                    <p>Thank you for paying your taxes on time. This receipt is an official proof of payment.</p>
                    <p>Bure Town Revenue Authority</p>
                </div>
            </div>
        </div>
        <div class="modal-footer no-print">
            <button class="btn btn-primary" onclick="TCMS.printReceipt('print-area-<?= $receipt['id'] ?>')">🖨 Print Receipt</button>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
