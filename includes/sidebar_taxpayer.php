<?php $base = str_repeat('../', max(0, substr_count(str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME'])), '/') - substr_count(str_replace('\\','/',realpath(__DIR__ . '/..')), '/') - 0)); ?>
<p class="nav-section-title">My Account</p>
<ul>
  <li><a href="<?= $base ?>taxpayer/dashboard.php"><span class="nav-icon">🏠</span> Dashboard</a></li>
  <li><a href="<?= $base ?>taxpayer/my_account.php"><span class="nav-icon">👤</span> My Profile</a></li>
</ul>
<p class="nav-section-title">Tax Payments</p>
<ul>
  <li><a href="<?= $base ?>taxpayer/pay_tax.php"><span class="nav-icon">💳</span> Pay Tax</a></li>
  <li><a href="<?= $base ?>taxpayer/payment_history.php"><span class="nav-icon">📜</span> Payment History</a></li>
  <li><a href="<?= $base ?>taxpayer/track_payment.php"><span class="nav-icon">🔍</span> Track Payment</a></li>
</ul>
<p class="nav-section-title">Documents</p>
<ul>
  <li><a href="<?= $base ?>taxpayer/receipts.php"><span class="nav-icon">🧾</span> Receipts</a></li>
</ul>
