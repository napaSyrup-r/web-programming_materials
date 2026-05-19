<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin · Lost & Found</title>
    <link rel="stylesheet" href="/assets/css/styles.css" />
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico" />
    <link rel="icon" type="image/png" href="/assets/img/logo.png" />
  </head>
  <body>
    <header class="app-header">
      <div class="app-header-inner container">
        <div class="brand"><img class="logo-small" src="/assets/img/logo.png" alt="Logo" />Admin · Lost & Found</div>
        <button class="menu-toggle" data-menu-toggle>☰</button>
        <nav class="nav">
          <a href="/admin/dashboard.php">Dashboard</a>
          <a href="/admin/items.php">Items</a>
          <a href="/admin/categories.php">Categories</a>
          <a href="/admin/users.php">Users</a>
          <a href="/admin/messages.php">Messages</a>
          <a href="/admin/claims.php">Claims</a>
          <a href="/admin/pages.php">Pages</a>
          <a href="/admin/settings.php">Settings</a>
          <a href="/admin/account.php">Account</a>
          <a href="/admin/logout.php">Logout</a>
        </nav>
        <button class="btn" title="Change theme" data-theme-toggle>🌙</button>
      </div>
    </header>
    <main class="container" style="padding-top: 1rem;">
      <?php if (!empty($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $k => $msg): unset($_SESSION['flash'][$k]); ?>
          <div class="card" style="margin-bottom: 1rem;">
            <div class="card-body">
              <div class="success"><?= htmlspecialchars($msg) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
