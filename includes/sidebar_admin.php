<?php
// Admin Sidebar Navigation
$base = str_repeat('../', max(0, substr_count(str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME'])), '/') - substr_count(str_replace('\\','/',realpath(__DIR__ . '/..')), '/') - 0));
?>
<p class="nav-section-title">Main</p>
<ul>
  <li><a href="<?= $base ?>admin/dashboard.php"><span class="nav-icon">📊</span> Dashboard</a></li>
</ul>
<p class="nav-section-title">User Management</p>
<ul>
  <li><a href="<?= $base ?>admin/manage_users.php"><span class="nav-icon">👥</span> All Users</a></li>
  <li><a href="<?= $base ?>admin/add_user.php"><span class="nav-icon">➕</span> Add User</a></li>
</ul>
<p class="nav-section-title">Tax Records</p>
<ul>
  <li><a href="<?= $base ?>admin/taxpayers.php"><span class="nav-icon">🧾</span> Taxpayers</a></li>
  <li><a href="<?= $base ?>admin/tax_records.php"><span class="nav-icon">📋</span> Tax Records</a></li>
  <li><a href="<?= $base ?>admin/payments.php"><span class="nav-icon">💳</span> Payments</a></li>
  <li><a href="<?= $base ?>admin/reports.php"><span class="nav-icon">📈</span> Reports</a></li>
</ul>
<p class="nav-section-title">System</p>
<ul>
  <li><a href="<?= $base ?>admin/news.php"><span class="nav-icon">📢</span> News & Notices</a></li>
  <li><a href="<?= $base ?>admin/audit_logs.php"><span class="nav-icon">🔍</span> Audit Logs</a></li>
  <li><a href="<?= $base ?>admin/settings.php"><span class="nav-icon">⚙️</span> Settings</a></li>
</ul>
