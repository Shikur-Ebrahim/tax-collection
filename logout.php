<?php
require_once 'config/auth.php';
$auth->logout();
header('Location: login.php?msg=logged_out');
exit;
