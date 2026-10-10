<?php
require_once __DIR__ . '/../config/auth.php';
$auth->requireRole('staff', '../login.php');

$db = Database::getInstance();
$userId = $auth->getUserId();

$myTodayCount   = $db->count('taxpayers', "registered_by = ? AND DATE(created_at) = CURRENT_DATE", [$userId]);
$totalTaxpayers = $db->count('taxpayers');
$pendingCount   = $db->count('taxpayers', "registration_status = 'pending'");
$approvedToday  = $db->count('taxpayers', "registration_status = 'approved' AND DATE(updated_at) = CURRENT_DATE");

$recentRegistrations = $db->fetchAll(
    "SELECT * FROM taxpayers WHERE registered_by = ? ORDER BY created_at DESC LIMIT 10",
    [$userId]
);
$latestNews = $db->fetchAll("SELECT * FROM news WHERE is_published = true ORDER BY published_at DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Staff Dashboard - Bure Town TCMS</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-layout">
<?php $sidebarRole='staff'; include __DIR__ . '/../includes/sidebar_staff.php'; ?>
<div class="main-content">
<?php $pageTitle='Staff Dashboard'; include __DIR__ . '/../includes/header.php'; ?>
<div class="content-area">

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card blue"><div class="stat-icon">📋</div><div class="stat-info"><h3><?= $myTodayCount ?></h3><p>My Registrations Today</p></div></div>
  <div class="stat-card green"><div class="stat-icon">👥</div><div class="stat-info"><h3><?= $totalTaxpayers ?></h3><p>Total Taxpayers</p></div></div>
  <div class="stat-card orange"><div class="stat-icon">⏳</div><div class="stat-info"><h3><?= $pendingCount ?></h3><p>Pending Approvals</p></div></div>
  <div class="stat-card teal"><div class="stat-icon">✅</div><div class="stat-info"><h3><?= $approvedToday ?></h3><p>Approved Today</p></div></div>
</div>

<!-- Quick Actions -->
<div class="card mb-24">
  <div class="card-header"><h3>⚡ Quick Actions</h3></div>
  <div class="card-body">
    <div class="form-grid" style="grid-template-columns:1fr 1fr; gap:12px;">
      <a href="register_taxpayer.php" class="btn btn-primary btn-lg" style="justify-content:center;">➕ Register New Taxpayer</a>
      <a href="taxpayer_list.php" class="btn btn-secondary btn-lg" style="justify-content:center;">📋 View Taxpayer List</a>
    </div>
  </div>
</div>

<div class="form-grid cols-2">
<!-- My Recent Registrations -->
<div class="card">
  <div class="card-header"><h3>My Recent Registrations</h3></div>
  <div class="card-body" style="padding:0">
    <table class="table">
      <thead><tr><th>TIN</th><th>Name</th><th>Type</th><th>Status</th></tr></thead>
      <tbody>
        <?php if(empty($recentRegistrations)): ?>
          <tr><td colspan="4" class="text-center text-muted">No registrations yet. <a href="register_taxpayer.php">Register one now!</a></td></tr>
        <?php else: foreach($recentRegistrations as $t): ?>
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

<!-- Latest News -->
<div>
  <h3 class="mb-16 text-primary">Latest Announcements</h3>
  <?php foreach($latestNews as $item): ?>
  <div class="news-card mb-12">
    <div class="news-card-header">
      <span class="category-badge <?= $item['category'] ?>"><?= ucfirst($item['category']) ?></span>
      <h3><?= htmlspecialchars($item['title']) ?></h3>
    </div>
    <p><?= htmlspecialchars(substr($item['content'], 0, 120)) ?>...</p>
  </div>
  <?php endforeach; ?>
</div>
</div>

</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</div>
</div>
</body>
</html>
