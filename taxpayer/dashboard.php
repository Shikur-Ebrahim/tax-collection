<?php
require_once __DIR__ . '/../config/auth.php';
$auth->requireRole('taxpayer');
$pageTitle = 'Taxpayer Dashboard';
$sidebarRole = 'taxpayer';
require_once __DIR__ . '/../includes/header.php';

$db = Database::getInstance();
$userId = $auth->getUserId();
$taxpayer = $db->fetchOne('SELECT * FROM taxpayers WHERE user_id = ?', [$userId]);

if (!$taxpayer) {
    echo "<div class='alert alert-warning'><span class='alert-icon'>⚠️</span> <div><strong>Not Registered</strong><br>You haven't completed your taxpayer registration yet. Please contact staff to complete your registration.</div></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Fetch stats
$totalDue = $db->fetchOne("SELECT SUM(total_due) as total FROM tax_records WHERE taxpayer_id = ? AND status != 'paid'", [$taxpayer['id']])['total'] ?? 0;
$paidThisYear = $db->fetchOne("SELECT SUM(amount_paid) as total FROM payments WHERE taxpayer_id = ? AND status = 'verified' AND YEAR(payment_date) = YEAR(CURRENT_DATE)", [$taxpayer['id']])['total'] ?? 0;
$recordsCount = $db->count('tax_records', 'taxpayer_id = ?', [$taxpayer['id']]);
$lastPayment = $db->fetchOne("SELECT MAX(payment_date) as last_date FROM payments WHERE taxpayer_id = ? AND status = 'verified'", [$taxpayer['id']])['last_date'];

// Fetch recent records
$recentRecords = $db->fetchAll("SELECT * FROM tax_records WHERE taxpayer_id = ? ORDER BY created_at DESC LIMIT 5", [$taxpayer['id']]);
$news = $db->fetchAll("SELECT * FROM news WHERE is_published = true ORDER BY published_at DESC LIMIT 3");
?>

<div class="stats-grid">
  <div class="stat-card blue">
    <div class="stat-icon">👤</div>
    <div class="stat-info">
      <h3><?= htmlspecialchars($taxpayer['tin']) ?></h3>
      <p>Your TIN Number</p>
    </div>
  </div>
  <div class="stat-card <?= $totalDue > 0 ? 'red' : 'green' ?>">
    <div class="stat-icon">💰</div>
    <div class="stat-info">
      <h3>ETB <?= number_format($totalDue, 2) ?></h3>
      <p>Outstanding Tax Due</p>
    </div>
  </div>
  <div class="stat-card green">
    <div class="stat-icon">💸</div>
    <div class="stat-info">
      <h3>ETB <?= number_format($paidThisYear, 2) ?></h3>
      <p>Paid This Year</p>
    </div>
  </div>
  <div class="stat-card orange">
    <div class="stat-icon">📋</div>
    <div class="stat-info">
      <h3><?= $recordsCount ?></h3>
      <p>Total Tax Records</p>
    </div>
  </div>
</div>

<?php if ($taxpayer['registration_status'] === 'pending'): ?>
<div class="alert alert-warning">
    <span class="alert-icon">⏳</span> 
    <div><strong>Registration Pending</strong><br>Your registration is currently under review by our staff. You will be able to process payments once approved.</div>
</div>
<?php elseif ($taxpayer['registration_status'] === 'rejected'): ?>
<div class="alert alert-danger">
    <span class="alert-icon">❌</span> 
    <div><strong>Registration Rejected</strong><br>Reason: <?= htmlspecialchars($taxpayer['rejection_reason'] ?? 'Not specified') ?>. Please visit the office.</div>
</div>
<?php endif; ?>

<div class="form-grid mb-24">
    <a href="pay_tax.php" class="btn btn-primary btn-block btn-lg">💳 Pay Tax Online</a>
    <a href="receipts.php" class="btn btn-secondary btn-block btn-lg">🧾 View Receipts</a>
    <a href="track_payment.php" class="btn btn-info btn-block btn-lg">🔍 Track Payment</a>
</div>

<div class="form-grid cols-2">
    <!-- Recent Tax Records -->
    <div class="card">
        <div class="card-header">
            <h3>Recent Tax Assessments</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Year/Period</th>
                            <th>Amount Due</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentRecords)): ?>
                            <tr><td colspan="4" class="text-center text-muted">No tax records found</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentRecords as $record): ?>
                                <tr>
                                    <td><?= $record['tax_year'] ?> (<?= $record['tax_period'] ?>)</td>
                                    <td class="fw-bold text-primary">ETB <?= number_format($record['total_due'], 2) ?></td>
                                    <td><?= $record['due_date'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= $record['status'] == 'paid' ? 'success' : ($record['status'] == 'assessed' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($record['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent News -->
    <div>
        <h3 class="mb-16 text-primary">Latest Announcements</h3>
        <?php foreach ($news as $item): ?>
            <div class="news-card mb-12">
                <div class="news-card-header">
                    <span class="category-badge <?= $item['category'] ?>"><?= ucfirst($item['category']) ?></span>
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                </div>
                <p><?= nl2br(htmlspecialchars(substr($item['content'], 0, 150))) ?>...</p>
                <div class="news-card-footer">
                    📅 <?= date('M j, Y', strtotime($item['published_at'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
