<?php $base = str_repeat('../', max(0, substr_count(str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME'])), '/') - substr_count(str_replace('\\','/',realpath(__DIR__ . '/..')), '/') - 0)); ?>
<p class="nav-section-title">Main</p>
<ul>
  <li><a href="<?= $base ?>staff/dashboard.php"><span class="nav-icon">📊</span> Dashboard</a></li>
</ul>
<p class="nav-section-title">Taxpayers</p>
<ul>
  <li><a href="<?= $base ?>staff/register_taxpayer.php"><span class="nav-icon">➕</span> Register Taxpayer</a></li>
  <li><a href="<?= $base ?>staff/taxpayer_list.php"><span class="nav-icon">📋</span> Taxpayer List</a></li>
</ul>
<p class="nav-section-title">Communication</p>
<ul>
  <li><a href="<?= $base ?>staff/news.php"><span class="nav-icon">📢</span> News & Notices</a></li>
</ul>
