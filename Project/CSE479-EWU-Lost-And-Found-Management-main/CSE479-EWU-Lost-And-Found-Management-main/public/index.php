<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

// Block public access when service is OFF and show reason
enforce_service_mode_or_show();

$cat = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Get categories for filter
$cats = query('SELECT id,name FROM categories WHERE is_active=1 ORDER BY name')->get_result();
$catAll = [];
while($c = $cats->fetch_assoc()) { $catAll[] = $c; }

$sql = "SELECT i.*, c.name as cat_name FROM items i JOIN categories c ON c.id=i.category_id WHERE i.is_hidden=0 AND i.status IN ('published','claim_pending','claimed')";
$params = [];
if ($cat) { $sql .= ' AND i.category_id=?'; $params[] = $cat; }
$sql .= ' ORDER BY i.created_at DESC';
$items = query($sql, $params)->get_result();

$home = query("SELECT title, content_html FROM pages WHERE slug='home' LIMIT 1")->get_result()->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lost & Found</title>
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
    <main class="container" style="padding-top:1rem;">
      <div class="slider" style="margin-bottom:1rem;">
        <div class="slide">Find What Matters</div>
        <div class="slide">Help Return Items</div>
        <div class="slide">Browse Unclaimed</div>
      </div>

      <div class="card"><div class="card-body">
        <h2 class="card-title" style="margin-bottom:0.4rem;"><?= esc($home['title'] ?? 'Welcome') ?></h2>
        <div class="muted"><?= $home['content_html'] ?? '' ?></div>
      </div></div>

      <div class="card" style="margin-top:1rem;">
        <div class="card-body">
          <form method="get" class="form" style="grid-template-columns: 1fr auto;">
            <select class="select" name="category">
              <option value="0">All Categories</option>
              <?php foreach($catAll as $opt): ?>
              <option value="<?= esc($opt['id']) ?>" <?= $cat===(int)$opt['id']? 'selected':'' ?>><?= esc($opt['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn" type="submit">Filter</button>
          </form>
        </div>
      </div>

      <div class="grid cards" style="margin-top:1rem;">
        <?php while($it = $items->fetch_assoc()): ?>
        <a class="card" href="/public/item.php?id=<?= (int)$it['id'] ?>" style="text-decoration:none; color:inherit;">
          <?php if ($it['photo']): ?>
          <img src="/<?= esc($it['photo']) ?>" alt="" style="width:100%;height:160px;object-fit:cover;" />
          <?php endif; ?>
          <div class="card-body">
            <div class="muted"><?= esc($it['cat_name']) ?></div>
            <div class="card-title"><?= esc($it['title']) ?></div>
            <div class="muted" style="min-height:2.6em;"> <?= esc(mb_strimwidth($it['description'],0,120,'…')) ?> </div>
            <?php if ($it['location_found']): ?><div class="muted">📍 <?= esc($it['location_found']) ?></div><?php endif; ?>
            <?php if ($it['date_found']): ?><div class="muted">📅 <?= esc($it['date_found']) ?></div><?php endif; ?>
            <?php if ($it['status']==='claim_pending'): ?><div class="badge yellow">Pending Claim</div><?php endif; ?>
            <?php if ($it['status']==='claimed'): ?><div class="badge green">Claimed</div><?php endif; ?>
          </div>
        </a>
        <?php endwhile; ?>
      </div>
    </main>
    <footer class="app-footer"><div class="container"><img class="logo-small" src="/assets/img/logo.png" alt="Logo" /> © <?= date('Y') ?> <strong><span>Azizul Abedin Azmi</span></strong></div></footer>
    <script src="/assets/js/app.js"></script>
  </body>
</html>
