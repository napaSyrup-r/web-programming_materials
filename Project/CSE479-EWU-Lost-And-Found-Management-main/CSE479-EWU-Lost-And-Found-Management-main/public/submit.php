<?php require_once __DIR__ . '/../includes/helpers.php'; enforce_service_mode_or_show(); $ok = isset($_GET['ok']); ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Post Found Item · Lost & Found</title>
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
    <main class="container" style="padding-top:1rem;max-width:820px;">
      <?php if ($ok): ?>
      <div class="card"><div class="card-body"><div class="success">Thank you! Your submission is pending review.</div></div></div>
      <?php endif; ?>
      <div class="card"><div class="card-body">
        <h2 class="card-title">Post a Found Item</h2>
        <form class="form" method="post" action="/api/submit_item.php" enctype="multipart/form-data">
          <div class="field"><label class="label">Title</label><input class="input" name="title" required /></div>
          <div class="grid" style="grid-template-columns:1fr 1fr; gap:0.6rem;">
            <div class="field"><label class="label">Category</label><input class="input" name="category_name" placeholder="e.g., Electronics" required /></div>
            <div class="field"><label class="label">Date Found</label><input class="input" type="date" name="date_found" /></div>
          </div>
          <div class="field"><label class="label">Location Found</label><input class="input" name="location_found" /></div>
          <div class="field"><label class="label">Description</label><textarea class="textarea" name="description" rows="6"></textarea></div>
          <div class="field"><label class="label">Photo</label><input class="file" type="file" name="photo" accept="image/*" /></div>
          <div class="grid" style="grid-template-columns:1fr 1fr; gap:0.6rem;">
            <div class="field"><label class="label">Your Name</label><input class="input" name="finder_name" required /></div>
            <div class="field"><label class="label">Your Contact</label><input class="input" name="finder_contact" required /></div>
          </div>
          <button class="btn primary" type="submit">Submit (Pending Approval)</button>
        </form>
      </div></div>
    </main>
    <footer class="app-footer"><div class="container"><img class="logo-small" src="/assets/img/logo.png" alt="Logo" /> © <?= date('Y') ?> <strong><span>Azizul Abedin Azmi</span></strong></div></footer>
  <script src="/assets/js/app.js"></script>
  </body>
</html>
