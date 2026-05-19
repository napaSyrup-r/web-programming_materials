<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'create' && is_post()) {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($name !== '') {
        query('INSERT INTO categories(name,description) VALUES(?,?)', [$name, $desc]);
        flash_set('ok', 'Category created.');
    }
    redirect('/admin/categories.php');
}

if ($action === 'update' && $id && is_post()) {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $active = isset($_POST['is_active']) ? 1 : 0;
    query('UPDATE categories SET name=?, description=?, is_active=? WHERE id=?', [$name, $desc, $active, $id]);
    flash_set('ok', 'Category updated.');
    redirect('/admin/categories.php');
}

if ($action === 'delete' && $id) {
    query('DELETE FROM categories WHERE id=?', [$id]);
    flash_set('ok', 'Category deleted.');
    redirect('/admin/categories.php');
}

include __DIR__ . '/includes/header.php';

$cats = query('SELECT * FROM categories ORDER BY created_at DESC')->get_result();
$edit = null;
if ($action === 'edit' && $id) {
    $edit = query('SELECT * FROM categories WHERE id=?', [$id])->get_result()->fetch_assoc();
}
?>

<h2 class="card-title">Categories</h2>
<div class="grid" style="grid-template-columns: 1.2fr 2fr;">
  <div>
    <div class="card">
      <div class="card-body">
        <h3 class="card-title" style="font-size:1.1rem;">Add Category</h3>
        <form class="form" method="post" action="?action=create">
          <div class="field">
            <label class="label">Name</label>
            <input class="input" name="name" required />
          </div>
          <div class="field">
            <label class="label">Description</label>
            <textarea class="textarea" name="description" rows="4"></textarea>
          </div>
          <button class="btn primary" type="submit">Create</button>
        </form>
      </div>
    </div>

    <?php if ($edit): ?>
    <div class="card" style="margin-top:1rem;">
      <div class="card-body">
        <h3 class="card-title" style="font-size:1.1rem;">Edit Category</h3>
        <form class="form" method="post" action="?action=update&id=<?= esc($edit['id']) ?>">
          <div class="field">
            <label class="label">Name</label>
            <input class="input" name="name" value="<?= esc($edit['name']) ?>" required />
          </div>
          <div class="field">
            <label class="label">Description</label>
            <textarea class="textarea" name="description" rows="4"><?= esc($edit['description']) ?></textarea>
          </div>
          <div class="field">
            <label class="label">Active</label>
            <label><input type="checkbox" name="is_active" <?= ($edit['is_active']? 'checked':'') ?> /> Active</label>
          </div>
          <button class="btn primary" type="submit">Update</button>
        </form>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div>
    <div class="card">
      <div class="card-body">
        <h3 class="card-title" style="font-size:1.1rem;">All Categories</h3>
        <table class="table">
          <thead>
            <tr><th>ID</th><th>Name</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php while($row = $cats->fetch_assoc()): ?>
            <tr>
              <td><?= esc($row['id']) ?></td>
              <td><?= esc($row['name']) ?></td>
              <td><?= $row['is_active']? '<span class="badge green">Active</span>' : '<span class="badge red">Inactive</span>' ?></td>
              <td>
                <a class="btn" href="?action=edit&id=<?= esc($row['id']) ?>">Edit</a>
                <a class="btn danger" href="?action=delete&id=<?= esc($row['id']) ?>" onclick="return confirm('Delete this category?');">Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
