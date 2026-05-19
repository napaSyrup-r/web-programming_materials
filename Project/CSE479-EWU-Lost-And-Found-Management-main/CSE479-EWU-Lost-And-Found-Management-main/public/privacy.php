<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
$page = query("SELECT title,content_html FROM pages WHERE slug='privacy' LIMIT 1")->get_result()->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= esc($page['title'] ?? 'Privacy Policy') ?> · Lost & Found</title>
    <link rel="stylesheet" href="/assets/css/styles.css" />
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico" />
    <link rel="icon" type="image/png" href="/assets/img/logo.png" />
  </head>
  <body>
    <header class="app-header">
      <div class="app-header-inner container">
        <div class="brand"><img class="logo-small" src="/assets/img/logo.png" alt="Logo" />Lost & Found</div>
        <button class="menu-toggle" data-menu-toggle>☰</button>
        <nav class="nav">
          <a href="/">Home</a>
          <a href="/public/about.php">About</a>
          <a href="/public/contact.php">Contact</a>
          <a href="/public/terms.php">Terms</a>
          <a href="/public/privacy.php">Policy</a>
          <a href="/public/submit.php">Post Found Item</a>
          <a href="/admin/">Admin</a>
        </nav>
        <button class="btn" title="Change theme" data-theme-toggle>🌙</button>
      </div>
    </header>
    <main class="container" style="padding-top:1rem;max-width:860px;">
      <div class="card"><div class="card-body">
        <h2 class="card-title"><?= esc($page['title'] ?? 'Privacy Policy') ?></h2>
        <div class="muted"><?= $page['content_html'] ?? '' ?></div>
      </div></div>
    </main>
    <footer class="app-footer"><div class="container"><img class="logo-small" src="/assets/img/logo.png" alt="Logo" /> © <?= date('Y') ?> <strong><span>Azizul Abedin Azmi</span></strong></div></footer>
  <script src="/assets/js/app.js"></script>
  </body>
</html>
