<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$__ensure = function() {
  // Create claims table if missing
  query("CREATE TABLE IF NOT EXISTS claims (\n    id INT AUTO_INCREMENT PRIMARY KEY,\n    item_id INT NOT NULL,\n    claimant_name VARCHAR(120) NOT NULL,\n    claimant_contact VARCHAR(160) NOT NULL,\n    evidence_text TEXT,\n    evidence_file VARCHAR(255) DEFAULT NULL,\n    status ENUM('new','approved','rejected','fulfilled') NOT NULL DEFAULT 'new',\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE\n  ) ENGINE=InnoDB");
  // Ensure items.status contains claim_pending option
  query("ALTER TABLE items MODIFY status ENUM('pending','published','claim_pending','claimed') NOT NULL DEFAULT 'pending'");
};
$__ensure();

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'approve' && $id) {
  // Approve claim: set other claims for same item to rejected (if new), set item to claim_pending
  $c = query('SELECT item_id FROM claims WHERE id=?', [$id])->get_result()->fetch_assoc();
  if ($c) {
    $itemId = (int)$c['item_id'];
    query("UPDATE claims SET status='rejected' WHERE item_id=? AND id<>? AND status='new'", [$itemId, $id]);
    query("UPDATE claims SET status='approved' WHERE id=?", [$id]);
    query("UPDATE items SET status='claim_pending' WHERE id=? AND status<>'claimed'", [$itemId]);
  }
  redirect('/admin/claims.php');
}

if ($action === 'reject' && $id) {
  query("UPDATE claims SET status='rejected' WHERE id=?", [$id]);
  redirect('/admin/claims.php');
}

if ($action === 'fulfill' && $id) {
  // When item is handed over, mark claim fulfilled and item claimed
  $c = query('SELECT item_id FROM claims WHERE id=?', [$id])->get_result()->fetch_assoc();
  if ($c) {
    $itemId = (int)$c['item_id'];
    query("UPDATE claims SET status='fulfilled' WHERE id=?", [$id]);
    query("UPDATE items SET status='claimed' WHERE id=?", [$itemId]);
  }
  redirect('/admin/claims.php');
}

if ($action === 'delete' && $id) {
  query('DELETE FROM claims WHERE id=?', [$id]);
  redirect('/admin/claims.php');
}

$claims = query('SELECT cl.*, i.title, i.status as item_status FROM claims cl JOIN items i ON i.id=cl.item_id ORDER BY cl.created_at DESC')->get_result();
include __DIR__ . '/includes/header.php';
?>

<h2 class="card-title">Claim Requests</h2>
<div class="card" style="margin-top:1rem;">
  <div class="card-body">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th><th>Item</th><th>Claimant</th><th>Contact</th><th>Status</th><th>Submitted</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($r = $claims->fetch_assoc()): ?>
        <tr>
          <td><?= esc($r['id']) ?></td>
          <td><?= esc($r['title']) ?></td>
          <td><?= esc($r['claimant_name']) ?></td>
          <td><?= esc($r['claimant_contact']) ?></td>
          <td>
            <?php if ($r['status']==='new'): ?><span class="badge yellow">New</span><?php endif; ?>
            <?php if ($r['status']==='approved'): ?><span class="badge">Approved</span><?php endif; ?>
            <?php if ($r['status']==='rejected'): ?><span class="badge red">Rejected</span><?php endif; ?>
            <?php if ($r['status']==='fulfilled'): ?><span class="badge green">Fulfilled</span><?php endif; ?>
          </td>
          <td><?= esc($r['created_at']) ?></td>
          <td>
            <a class="btn" href="#" onclick="alert(<?= json_encode($r['evidence_text']) ?>);return false;">View Evidence</a>
            <?php if (!empty($r['evidence_file'])): ?><a class="btn" target="_blank" href="/<?= esc($r['evidence_file']) ?>">Attachment</a><?php endif; ?>
            <?php if ($r['status']==='new'): ?><a class="btn" href="?action=approve&id=<?= esc($r['id']) ?>">Approve</a><?php endif; ?>
            <?php if ($r['status']==='new' || $r['status']==='approved'): ?><a class="btn danger" href="?action=reject&id=<?= esc($r['id']) ?>">Reject</a><?php endif; ?>
            <?php if ($r['status']==='approved'): ?><a class="btn" href="?action=fulfill&id=<?= esc($r['id']) ?>">Mark Handed Over</a><?php endif; ?>
            <a class="btn" href="?action=delete&id=<?= esc($r['id']) ?>" onclick="return confirm('Delete this record?');">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
