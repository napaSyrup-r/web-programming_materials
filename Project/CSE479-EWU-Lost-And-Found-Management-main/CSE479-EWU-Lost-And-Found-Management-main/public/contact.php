<?php
require_once __DIR__ . '/../includes/helpers.php';
$enforce = true; if ($enforce) { enforce_service_mode_or_show(); }
$ok = isset($_GET['ok']);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact · Lost & Found</title>
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
          <a href="/public/submit.php">Post Found Item</a>
          <a href="/admin/">Admin</a>
        </nav>
        <button class="btn" title="Change theme" data-theme-toggle>🌙</button>
      </div>
    </header>
    <main class="container" style="padding-top:1rem;max-width:720px;">
      <?php if ($ok): ?>
      <div class="card"><div class="card-body"><div class="success">Message sent! We will get back to you soon.</div></div></div>
      <?php endif; ?>
      <div class="card"><div class="card-body">
        <h2 class="card-title">Contact Us</h2>
        <form class="form" method="post" action="/api/send_message.php">
          <div class="grid" style="grid-template-columns:1fr 1fr; gap:0.6rem;">
            <div class="field"><label class="label">Your Name</label><input class="input" name="name" required /></div>
            <div class="field"><label class="label">Email</label><input class="input" type="email" name="email" required /></div>
          </div>
          <div class="field"><label class="label">Subject</label><input class="input" name="subject" /></div>
          <div class="field"><label class="label">Message</label><textarea class="textarea" name="message" rows="6" required></textarea></div>
          <button class="btn primary" type="submit">Send</button>
        </form>
      </div></div>
    </main>
    <footer class="app-footer"><div class="container"><img class="logo-small" src="/assets/img/logo.png" alt="Logo" /> © <?= date('Y') ?> <strong><span>Azizul Abedin Azmi</span></strong></div></footer>
  <script src="/assets/js/app.js"></script>
  </body>
</html>
