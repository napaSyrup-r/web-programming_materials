<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
// Enforce service mode for public
enforce_service_mode_or_show();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$item = null;
if ($id) {
  $item = query('SELECT i.*, c.name as cat_name FROM items i JOIN categories c ON c.id=i.category_id WHERE i.id=? LIMIT 1', [$id])->get_result()->fetch_assoc();
}
if (!$item) { render_not_found(); }
$ok = isset($_GET['claimed']);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= esc($item['title']) ?> · Lost & Found</title>
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
          <a href="/public/">Browse</a>
          <a href="/public/contact.php">Contact</a>
        </nav>
        <button class="btn" title="Change theme" data-theme-toggle>🌙</button>
      </div>
    </header>
    <main class="container" style="padding-top:1rem;max-width:860px;">
      <?php if ($ok): ?>
      <div class="card"><div class="card-body"><div class="success">Your claim request was sent. We will review it soon.</div></div></div>
      <?php endif; ?>
      <div class="card"><div class="card-body">
        <?php if ($item['photo']): ?><img src="/<?= esc($item['photo']) ?>" alt="" style="width:100%;max-height:360px;object-fit:cover;border-radius:12px;" /><?php endif; ?>
        <h2 class="card-title" style="margin-top:.5rem;"><?= esc($item['title']) ?></h2>
        <div class="muted">Category: <?= esc($item['cat_name']) ?></div>
        <?php if ($item['location_found']): ?><div class="muted">📍 <?= esc($item['location_found']) ?></div><?php endif; ?>
        <?php if ($item['date_found']): ?><div class="muted">📅 <?= esc($item['date_found']) ?></div><?php endif; ?>
        <p class="muted" style="margin-top:.5rem;"><?= nl2br(esc($item['description'])) ?></p>
        <hr style="border-color:rgba(148,163,184,0.15);margin:1rem 0;" />
        <?php if ($item['status']==='claimed'): ?>
          <div class="badge green">Claimed</div>
        <?php elseif ($item['status']==='claim_pending'): ?>
          <div class="badge yellow">Pending handover (approved claim)</div>
        <?php else: ?>
          <h3 class="card-title">Request to Claim</h3>
          <p class="muted">If this is yours, submit strong evidence (purchase receipt, unique identifiers, or photos). Admin will review and respond.</p>
          <form class="form" method="post" action="/api/submit_claim.php" enctype="multipart/form-data">
            <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>" />
            <div class="grid" style="grid-template-columns:1fr 1fr; gap:0.6rem;">
              <div class="field"><label class="label">Your Name</label><input class="input" name="claimant_name" required /></div>
              <div class="field"><label class="label">Contact</label><input class="input" name="claimant_contact" required /></div>
            </div>
            <div class="field"><label class="label">Evidence / Proof</label><textarea class="textarea" name="evidence_text" rows="6" placeholder="Describe evidence that proves ownership" required></textarea></div>
            <div class="field"><label class="label">Attach File (optional)</label><input class="file" type="file" name="evidence_file" accept="image/*,.pdf" /></div>
            <button class="btn primary" type="submit">Submit Claim Request</button>
          </form>
        <?php endif; ?>
      </div></div>
    </main>
    <footer class="app-footer"><div class="container"><img class="logo-small" src="/assets/img/logo.png" alt="Logo" /> © <?= date('Y') ?> <strong><span>Azizul Abedin Azmi</span></strong></div></footer>
    <script src="/assets/js/app.js"></script>
  </body>
</html>
