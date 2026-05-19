<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'read' && $id) {
    query('UPDATE messages SET read_at=NOW() WHERE id=? AND read_at IS NULL', [$id]);
    redirect('/admin/messages.php');
}

if ($action === 'unread' && $id) {
  query('UPDATE messages SET read_at=NULL WHERE id=? AND read_at IS NOT NULL', [$id]);
  redirect('/admin/messages.php');
}

if ($action === 'delete' && $id) {
    query('DELETE FROM messages WHERE id=?', [$id]);
    flash_set('ok','Message deleted.');
    redirect('/admin/messages.php');
}

$msgs = query('SELECT * FROM messages ORDER BY created_at DESC')->get_result();
include __DIR__ . '/includes/header.php';
?>

<h2 class="card-title">Messages / Inquiries</h2>
<div class="card" style="margin-top:1rem;">
  <div class="card-body">
    <table class="table">
      <thead>
        <tr><th>ID</th><th>From</th><th>Email</th><th>Subject</th><th>Status</th><th>Received</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while($m = $msgs->fetch_assoc()): ?>
        <tr>
          <td><?= esc($m['id']) ?></td>
          <td><?= esc($m['name']) ?></td>
          <td><?= esc($m['email']) ?></td>
          <td><?= esc($m['subject']) ?></td>
          <td><?= $m['read_at']? '<span class="badge">Read</span>':'<span class="badge yellow">New</span>' ?></td>
          <td><?= esc($m['created_at']) ?></td>
          <td>
            <?php if (!$m['read_at']): ?>
              <a class="btn" href="?action=read&id=<?= esc($m['id']) ?>">Mark Read</a>
            <?php else: ?>
              <a class="btn" href="?action=unread&id=<?= esc($m['id']) ?>">Mark Unread</a>
            <?php endif; ?>
            <a class="btn danger" href="?action=delete&id=<?= esc($m['id']) ?>" onclick="return confirm('Delete this message?');">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
