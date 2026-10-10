<?php
// ============================================================
// Shared Header Include
// Bure Town Tax Collection Management System
// ============================================================

// Determine base path relative to file depth
$depth = substr_count(str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME'])), '/');
$rootDepth = substr_count(str_replace('\\','/',realpath(__DIR__ . '/..')), '/');
$relBase = str_repeat('../', max(0, $depth - $rootDepth - 0));

// Page title default
if (!isset($pageTitle)) $pageTitle = 'Dashboard';
if (!isset($sidebarRole)) $sidebarRole = $auth->getRole();
$currentUser = $auth->getUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> — BTCMS</title>
  <link rel="stylesheet" href="<?= $relBase ?>assets/css/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏛️</text></svg>">
</head>
<body>
<div class="app-wrapper">
  <!-- ======= SIDEBAR ======= -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">🏛</div>
      <div class="logo-text">
        <h2>Bure Town TCMS</h2>
        <span>Tax Collection System</span>
      </div>
    </div>
    <div class="sidebar-user">
      <div class="avatar"><?= strtoupper(substr($currentUser['full_name'] ?? 'U', 0, 1)) ?></div>
      <div class="user-info">
        <h4><?= htmlspecialchars($currentUser['full_name'] ?? 'User') ?></h4>
        <span>@<?= htmlspecialchars($currentUser['username'] ?? '') ?></span>
      </div>
      <span class="badge-role"><?= htmlspecialchars($sidebarRole) ?></span>
    </div>
    <nav class="sidebar-nav">
      <?php include __DIR__ . '/sidebar_' . $sidebarRole . '.php'; ?>
    </nav>
    <div class="sidebar-footer">
      <a href="<?= $relBase ?>logout.php">🚪 Sign Out</a>
    </div>
  </aside>

  <!-- ======= MAIN ======= -->
  <div class="main-content">
    <header class="topbar">
      <div>
        <div class="topbar-title">
          <?= htmlspecialchars($pageTitle) ?>
          <small><?= date('l, F j, Y') ?></small>
        </div>
      </div>
      <div class="topbar-actions">
        <span class="topbar-time" id="topbar-time"></span>
        <button class="btn-icon" id="sidebar-toggle" title="Toggle Sidebar">☰</button>
        <button class="btn-icon" title="Notifications">
          🔔<span class="notif-dot"></span>
        </button>
        <a class="btn-icon" href="<?= $relBase ?>logout.php" title="Logout">🚪</a>
      </div>
    </header>
    <main class="page-content">
