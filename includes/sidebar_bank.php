<?php $base = str_repeat('../', max(0, substr_count(str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME'])), '/') - substr_count(str_replace('\\','/',realpath(__DIR__ . '/..')), '/') - 0)); ?>
<p class="nav-section-title">Main</p>
<ul>
  <li><a href="<?= $base ?>bank/dashboard.php"><span class="nav-icon">🏦</span> Dashboard</a></li>
</ul>
<p class="nav-section-title">Payments</p>
<ul>
  <li><a href="<?= $base ?>bank/verify_payment.php"><span class="nav-icon">✅</span> Verify Payment</a></li>
  <li><a href="<?= $base ?>bank/payment_list.php"><span class="nav-icon">📋</span> Payment List</a></li>
</ul>
