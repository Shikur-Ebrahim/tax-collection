<?php
require_once __DIR__ . '/../config/auth.php';
$auth->requireRole('admin', '../login.php');

$db = Database::getInstance();
$pageTitle = 'Admin Dashboard';

// Fetch stats
$totalUsers     = $db->count('users');
$totalTaxpayers = $db->count('taxpayers');
$totalRevenue   = $db->fetchOne("SELECT COALESCE(SUM(amount_paid),0) AS total FROM payments WHERE status='verified'")['total'] ?? 0;
$pendingApprove = $db->count('taxpayers', "registration_status = 'pending'");

$recentTaxpayers = $db->fetchAll("SELECT t.*, u.full_name as reg_by FROM taxpayers t LEFT JOIN users u ON t.registered_by = u.id ORDER BY t.created_at DESC LIMIT 10");
$recentNews      = $db->fetchAll("SELECT * FROM news ORDER BY published_at DESC LIMIT 3");
$allUsers        = $db->fetchAll("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard - Bure Town TCMS</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-layout">
<?php $sidebarRole='admin'; include __DIR__ . '/../includes/sidebar_admin.php'; ?>
<div class="main-content">
<?php $pageTitle='Admin Dashboard'; include __DIR__ . '/../includes/header.php'; ?>
<div class="content-area">

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card green"><div class="stat-icon">👥</div><div class="stat-info"><h3><?= $totalUsers ?></h3><p>Total Users</p></div></div>
  <div class="stat-card blue"><div class="stat-icon">🧾</div><div class="stat-info"><h3><?= $totalTaxpayers ?></h3><p>Total Taxpayers</p></div></div>
  <div class="stat-card orange"><div class="stat-icon">💰</div><div class="stat-info"><h3>ETB <?= number_format($totalRevenue,2) ?></h3><p>Revenue Collected</p></div></div>
  <div class="stat-card red"><div class="stat-icon">⏳</div><div class="stat-info"><h3><?= $pendingApprove ?></h3><p>Pending Approvals</p></div></div>
</div>

<!-- Quick Actions -->
<div class="card mb-24">
  <div class="card-header"><h3>⚡ Quick Actions</h3></div>
  <div class="card-body">
    <div class="form-grid" style="grid-template-columns: repeat(4,1fr); gap:12px;">
      <a href="manage_users.php" class="btn btn-primary">👥 Manage Users</a>
      <a href="taxpayers.php" class="btn btn-secondary">🧾 Taxpayers</a>
      <a href="news.php" class="btn btn-info">📰 Manage News</a>
      <a href="reports.php" class="btn btn-outline-primary">📊 Reports</a>
    </div>
  </div>
</div>

<div class="form-grid cols-2">
<!-- Recent Users -->
<div class="card">
  <div class="card-header"><h3>Recent Users</h3></div>
  <div class="card-body" style="padding:0">
    <table class="table">
      <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach($allUsers as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['full_name']) ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><span class="badge badge-info"><?= htmlspecialchars($u['role_name']) ?></span></td>
          <td><span class="badge badge-<?= $u['status']==='active'?'success':'danger' ?>"><?= $u['status'] ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Recent Taxpayers -->
<div class="card">
  <div class="card-header"><h3>Recent Taxpayer Registrations</h3></div>
  <div class="card-body" style="padding:0">
    <table class="table">
      <thead><tr><th>TIN</th><th>Name</th><th>Type</th><th>Status</th></tr></thead>
      <tbody>
        <?php if(empty($recentTaxpayers)): ?>
          <tr><td colspan="4" class="text-center text-muted">No taxpayers yet</td></tr>
        <?php else: foreach($recentTaxpayers as $t): ?>
        <tr>
          <td class="fw-bold"><?= htmlspecialchars($t['tin']) ?></td>
          <td><?= htmlspecialchars($t['full_name']) ?></td>
          <td><?= htmlspecialchars($t['taxpayer_type']) ?></td>
          <td><span class="badge badge-<?= $t['registration_status']==='approved'?'success':($t['registration_status']==='pending'?'warning':'danger') ?>"><?= $t['registration_status'] ?></span></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
