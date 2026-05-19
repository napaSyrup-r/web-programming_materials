<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$totalItems = query('SELECT COUNT(*) c FROM items')->get_result()->fetch_assoc()['c'] ?? 0;
$publishedItems = query("SELECT COUNT(*) c FROM items WHERE status='published'")->get_result()->fetch_assoc()['c'] ?? 0;
$pendingItems = query("SELECT COUNT(*) c FROM items WHERE status='pending'")->get_result()->fetch_assoc()['c'] ?? 0;
$claimedItems = query("SELECT COUNT(*) c FROM items WHERE status='claimed'")->get_result()->fetch_assoc()['c'] ?? 0;
$pendingClaims = query("SELECT COUNT(*) c FROM claims WHERE status='new'")->get_result()->fetch_assoc()['c'] ?? 0;
$totalCats = query('SELECT COUNT(*) c FROM categories')->get_result()->fetch_assoc()['c'] ?? 0;
$totalUsers = query('SELECT COUNT(*) c FROM users')->get_result()->fetch_assoc()['c'] ?? 0;
$unreadMsgs = query('SELECT COUNT(*) c FROM messages WHERE read_at IS NULL')->get_result()->fetch_assoc()['c'] ?? 0;

include __DIR__ . '/includes/header.php';
?>

<h2 class="card-title">Dashboard</h2>
<div class="grid cards" style="margin-top: 1rem;">
  <a class="card" href="/admin/items.php" style="text-decoration:none;">
    <div class="card-body"><div class="muted">Items</div><div class="card-title"><?= esc($totalItems) ?></div></div>
  </a>
  <a class="card" href="/admin/items.php" style="text-decoration:none;">
    <div class="card-body"><div class="muted">Published</div><div class="card-title"><span class="badge green"><?= esc($publishedItems) ?></span></div></div>
  </a>
  <a class="card" href="/admin/items.php" style="text-decoration:none;">
    <div class="card-body"><div class="muted">Pending</div><div class="card-title"><span class="badge yellow"><?= esc($pendingItems) ?></span></div></div>
  </a>
  <a class="card" href="/admin/items.php" style="text-decoration:none;">
    <div class="card-body"><div class="muted">Claimed</div><div class="card-title"><span class="badge"><?= esc($claimedItems) ?></span></div></div>
  </a>
  <a class="card" href="/admin/categories.php" style="text-decoration:none;">
    <div class="card-body"><div class="muted">Categories</div><div class="card-title"><?= esc($totalCats) ?></div></div>
  </a>
  <a class="card" href="/admin/users.php" style="text-decoration:none;">
    <div class="card-body"><div class="muted">Users</div><div class="card-title"><?= esc($totalUsers) ?></div></div>
  </a>
  <a class="card" href="/admin/messages.php" style="text-decoration:none;">
    <div class="card-body"><div class="muted">Unread Messages</div><div class="card-title"><?= esc($unreadMsgs) ?></div></div>
  </a>
  <a class="card" href="/admin/claims.php" style="text-decoration:none;">
    <div class="card-body"><div class="muted">New Claims</div><div class="card-title"><span class="badge yellow"><?= esc($pendingClaims) ?></span></div></div>
  </a>
</div>

<div class="card" style="margin-top: 1rem;">
  <div class="card-body">
    <div class="slider">
      <div class="slide">Keep data tidy and updated</div>
      <div class="slide">Approve legit found items</div>
      <div class="slide">Respond to inquiries</div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
