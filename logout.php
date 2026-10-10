<?php
require_once __DIR__ . '/config/auth.php';
$auth->logout();
header('Location: login.php?msg=logged_out');
exit;
