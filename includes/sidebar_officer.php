<?php $base = str_repeat('../', max(0, substr_count(str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME'])), '/') - substr_count(str_replace('\\','/',realpath(__DIR__ . '/..')), '/') - 0)); ?>
<p class="nav-section-title">Main</p>
<ul>
  <li><a href="<?= $base ?>officer/dashboard.php"><span class="nav-icon">📊</span> Dashboard</a></li>
</ul>
<p class="nav-section-title">Tax Management</p>
<ul>
  <li><a href="<?= $base ?>officer/calculate_tax.php"><span class="nav-icon">🧮</span> Calculate Tax</a></li>
  <li><a href="<?= $base ?>officer/tax_records.php"><span class="nav-icon">📋</span> Tax Records</a></li>
  <li><a href="<?= $base ?>officer/taxpayer_list.php"><span class="nav-icon">👤</span> Taxpayers</a></li>
</ul>
<p class="nav-section-title">Reports</p>
<ul>
  <li><a href="<?= $base ?>officer/generate_reports.php"><span class="nav-icon">📈</span> Generate Report</a></li>
  <li><a href="<?= $base ?>officer/view_reports.php"><span class="nav-icon">📄</span> View Reports</a></li>
</ul>
