<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$__ensure_hidden = function() {
  // Ensure hidden flag exists (check before altering to avoid duplicate errors)
  $exists = query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='items' AND COLUMN_NAME='is_hidden'")->get_result()->fetch_assoc();
  if (!$exists) {
    query("ALTER TABLE items ADD COLUMN is_hidden TINYINT(1) NOT NULL DEFAULT 0");
  }
};
$__ensure_hidden();

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ensure uploads dir exists
$uploadDir = __DIR__ . '/../uploads';
if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }

if ($action === 'create' && is_post()) {
    $title = trim($_POST['title'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    $finder_name = trim($_POST['finder_name'] ?? '');
    $finder_contact = trim($_POST['finder_contact'] ?? '');
    $location_found = trim($_POST['location_found'] ?? '');
    $date_found = trim($_POST['date_found'] ?? '');
    $photoPath = null;
    if (!empty($_FILES['photo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($ext, $allowed)) {
            $name = 'item_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            $dest = $uploadDir . '/' . $name;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                $photoPath = 'uploads/' . $name;
            }
        }
    }
    query('INSERT INTO items (category_id,title,description,photo, finder_name,finder_contact,location_found,date_found,status) VALUES (?,?,?,?,?,?,?,?,\'pending\')',
        [$category_id,$title,$desc,$photoPath,$finder_name,$finder_contact,$location_found,$date_found]);
    flash_set('ok','Item created (pending).');
    redirect('/admin/items.php');
}

if ($action === 'delete' && $id) {
    // delete file if exists
    $row = query('SELECT photo FROM items WHERE id=?', [$id])->get_result()->fetch_assoc();
    if ($row && !empty($row['photo'])) {
        $file = __DIR__ . '/../' . $row['photo'];
        if (is_file($file)) @unlink($file);
    }
    query('DELETE FROM items WHERE id=?', [$id]);
    flash_set('ok', 'Item deleted.');
    redirect('/admin/items.php');
}

if ($action === 'publish' && $id) {
    $uid = current_user()['id'];
    query("UPDATE items SET status='published', approved_by=?, approved_at=NOW() WHERE id=?", [$uid, $id]);
    flash_set('ok','Item published.');
    redirect('/admin/items.php');
}

if ($action === 'claim' && $id) {
    query("UPDATE items SET status='claimed' WHERE id=?", [$id]);
    flash_set('ok','Item marked as claimed.');
    redirect('/admin/items.php');
}

if ($action === 'hide' && $id) {
  query("UPDATE items SET is_hidden=1 WHERE id=?", [$id]);
  flash_set('ok','Item hidden from public.');
  redirect('/admin/items.php');
}

if ($action === 'unhide' && $id) {
  query("UPDATE items SET is_hidden=0 WHERE id=?", [$id]);
  flash_set('ok','Item visible to public.');
  redirect('/admin/items.php');
}

$cats = query('SELECT id,name FROM categories WHERE is_active=1 ORDER BY name')->get_result();
$catOptions = [];
while($c = $cats->fetch_assoc()) { $catOptions[] = $c; }

include __DIR__ . '/includes/header.php';

// Filtering & sorting
$f_title = trim($_GET['f_title'] ?? '');
$f_category = isset($_GET['f_category']) ? (int)$_GET['f_category'] : 0;
$f_status = trim($_GET['f_status'] ?? '');
$f_year = trim($_GET['f_year'] ?? '');
$f_month = trim($_GET['f_month'] ?? ''); // 1-12
$f_date_from = trim($_GET['f_date_from'] ?? '');
$f_date_to = trim($_GET['f_date_to'] ?? '');
$sort_by = $_GET['sort_by'] ?? 'created_at'; // created_at | date_found
$sort_dir = strtolower($_GET['sort_dir'] ?? 'desc'); // asc | desc
$sort_dir = $sort_dir === 'asc' ? 'ASC' : 'DESC';

$sql = 'SELECT i.*, c.name as cat_name FROM items i JOIN categories c ON c.id=i.category_id';
$params = [];
$w = [];
if ($f_title !== '') { $w[] = 'i.title LIKE ?'; $params[] = '%'.$f_title.'%'; }
if ($f_category) { $w[] = 'i.category_id=?'; $params[] = $f_category; }
if ($f_status !== '') { $w[] = 'i.status=?'; $params[] = $f_status; }
if ($f_year !== '') { $w[] = 'YEAR(i.created_at)=?'; $params[] = (int)$f_year; }
if ($f_month !== '') { $w[] = 'MONTH(i.created_at)=?'; $params[] = (int)$f_month; }
if ($f_date_from !== '') { $w[] = 'i.date_found>=?'; $params[] = $f_date_from; }
if ($f_date_to !== '') { $w[] = 'i.date_found<=?'; $params[] = $f_date_to; }
if ($w) { $sql .= ' WHERE ' . implode(' AND ', $w); }
$orderCol = $sort_by === 'date_found' ? 'i.date_found' : 'i.created_at';
$sql .= ' ORDER BY ' . $orderCol . ' ' . $sort_dir;
$items = query($sql, $params)->get_result();
?>

<h2 class="card-title">Items</h2>

<div class="grid" style="grid-template-columns: 1.2fr 2fr;">
  <div>
    <div class="card">
      <div class="card-body">
        <h3 class="card-title" style="font-size:1.1rem;">Add New Item</h3>
        <form class="form" method="post" action="?action=create" enctype="multipart/form-data">
          <div class="field">
            <label class="label">Title</label>
            <input class="input" name="title" required />
          </div>
          <div class="field">
            <label class="label">Category</label>
            <select class="select" name="category_id" required>
              <option value="">Select Category</option>
              <?php foreach($catOptions as $opt): ?>
              <option value="<?= esc($opt['id']) ?>"><?= esc($opt['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label class="label">Description</label>
            <textarea class="textarea" name="description" rows="4"></textarea>
          </div>
          <div class="field">
            <label class="label">Photo</label>
            <input class="file" type="file" name="photo" accept="image/*" />
            <div class="help">Optional. JPG/PNG/GIF.</div>
          </div>
          <div class="grid" style="grid-template-columns:1fr 1fr; gap:0.6rem;">
            <div class="field">
              <label class="label">Finder Name</label>
              <input class="input" name="finder_name" />
            </div>
            <div class="field">
              <label class="label">Finder Contact</label>
              <input class="input" name="finder_contact" />
            </div>
          </div>
          <div class="grid" style="grid-template-columns:1fr 1fr; gap:0.6rem;">
            <div class="field">
              <label class="label">Location Found</label>
              <input class="input" name="location_found" />
            </div>
            <div class="field">
              <label class="label">Date Found</label>
              <input class="input" type="date" name="date_found" />
            </div>
          </div>
          <button class="btn primary" type="submit">Create (Pending)</button>
        </form>
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-body">
        <h3 class="card-title" style="font-size:1.1rem;">All Items</h3>
        <form class="form" method="get" style="margin-bottom:0.8rem;">
          <div class="grid" style="grid-template-columns:1.2fr 1fr 1fr 1fr 1fr auto; gap:0.6rem;">
            <div class="field"><label class="label">Title</label><input class="input" name="f_title" value="<?= esc($f_title) ?>" placeholder="Search title" /></div>
            <div class="field"><label class="label">Category</label>
              <select class="select" name="f_category">
                <option value="0">All</option>
                <?php foreach($catOptions as $opt): ?>
                  <option value="<?= esc($opt['id']) ?>" <?= $f_category===(int)$opt['id']? 'selected':'' ?>><?= esc($opt['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field"><label class="label">Status</label>
              <select class="select" name="f_status">
                <option value="">All</option>
                <option value="pending" <?= $f_status==='pending'? 'selected':'' ?>>Pending</option>
                <option value="published" <?= $f_status==='published'? 'selected':'' ?>>Published</option>
                <option value="claim_pending" <?= $f_status==='claim_pending'? 'selected':'' ?>>Pending Claim</option>
                <option value="claimed" <?= $f_status==='claimed'? 'selected':'' ?>>Claimed</option>
              </select>
            </div>
            <div class="field"><label class="label">Year (created)</label><input class="input" name="f_year" value="<?= esc($f_year) ?>" placeholder="YYYY" /></div>
            <div class="field"><label class="label">Month (created)</label><input class="input" name="f_month" value="<?= esc($f_month) ?>" placeholder="1-12" /></div>
            <div class="field" style="grid-column: 1 / -1;">
              <label class="label">Date Found Range</label>
              <div class="grid" style="grid-template-columns:1fr 1fr; gap:0.6rem;">
                <input class="input" type="date" name="f_date_from" value="<?= esc($f_date_from) ?>" />
                <input class="input" type="date" name="f_date_to" value="<?= esc($f_date_to) ?>" />
              </div>
            </div>
            <div style="align-self:end"><button class="btn" type="submit">Search</button> <a class="btn" href="/admin/items.php">Reset</a></div>
          </div>
        </form>
         <div class="card" style="padding:1rem; border-radius:12px;">
         <table class="table" style="font-size:0.95rem;">
          <thead>
            <tr>
              <th>ID</th><th>Photo</th><th>Title</th><th>Category</th><th>Status</th>
              <th>
                <a href="?<?= http_build_query(array_merge($_GET, ['sort_by'=>'date_found','sort_dir'=>($sort_by==='date_found' && strtolower($sort_dir)==='asc' ? 'desc':'asc')])) ?>">Dates (Found | Created)</a>
              </th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($it = $items->fetch_assoc()): ?>
            <tr style="height:84px;">
              <td><?= esc($it['id']) ?></td>
              <td><?php if ($it['photo']): ?><img src="/<?= esc($it['photo']) ?>" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:10px;" /><?php endif; ?></td>
              <td><?= esc($it['title']) ?></td>
              <td><?= esc($it['cat_name']) ?></td>
              <td>
                 <?php if ($it['status']==='published'): ?><span class="badge green">Published</span>
                 <?php elseif ($it['status']==='pending'): ?><span class="badge yellow">Pending</span>
                 <?php elseif ($it['status']==='claim_pending'): ?><span class="badge yellow">Pending Claim</span>
                 <?php else: ?><span class="badge">Claimed</span><?php endif; ?>
                 <?php if (!empty($it['is_hidden']) && (int)$it['is_hidden']===1): ?> <span class="badge red">Hidden</span><?php endif; ?>
              </td>
              <td>
                <?= esc($it['date_found'] ?? '') ?> | <?= esc($it['created_at'] ?? '') ?>
              </td>
              <td>
                <?php if ($it['status']==='pending'): ?>
                  <a class="btn" href="?action=publish&id=<?= esc($it['id']) ?>">Publish</a>
                <?php endif; ?>
                <?php if ($it['status']!=='claimed'): ?>
                  <a class="btn" href="?action=claim&id=<?= esc($it['id']) ?>">Mark Claimed</a>
                <?php endif; ?>
                <?php if (empty($it['is_hidden']) || (int)$it['is_hidden']===0): ?>
                  <a class="btn" href="?action=hide&id=<?= esc($it['id']) ?>">Hide</a>
                <?php else: ?>
                  <a class="btn" href="?action=unhide&id=<?= esc($it['id']) ?>">Unhide</a>
                <?php endif; ?>
                <a class="btn danger" href="?action=delete&id=<?= esc($it['id']) ?>" onclick="return confirm('Delete this item?');">Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
         </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
