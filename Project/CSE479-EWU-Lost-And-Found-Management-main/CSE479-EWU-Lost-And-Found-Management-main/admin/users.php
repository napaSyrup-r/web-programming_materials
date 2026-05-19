<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
require_role('admin');

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'create' && is_post()) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = ($_POST['role'] ?? 'staff') === 'admin' ? 'admin' : 'staff';
    if ($name && $username && $password) {
        query('INSERT INTO users(name,email,username,password,role) VALUES (?,?,?,?,?)', [$name,$email,$username,$password,$role]);
        flash_set('ok','User created.');
    }
    redirect('/admin/users.php');
}

if ($action === 'delete' && $id) {
    // prevent deleting current user
    if ($id === (int)current_user()['id']) {
        flash_set('ok', 'You cannot delete your own account.');
    } else {
        query('DELETE FROM users WHERE id=?', [$id]);
        flash_set('ok','User deleted.');
    }
    redirect('/admin/users.php');
}

if ($action === 'update' && $id && is_post()) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = ($_POST['role'] ?? 'staff') === 'admin' ? 'admin' : 'staff';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    query('UPDATE users SET name=?, email=?, role=?, is_active=? WHERE id=?', [$name,$email,$role,$is_active,$id]);
    if (!empty($_POST['password'])) {
        $password = trim($_POST['password']);
        query('UPDATE users SET password=? WHERE id=?', [$password, $id]);
    }
    flash_set('ok','User updated.');
    redirect('/admin/users.php');
}

$users = query('SELECT * FROM users ORDER BY created_at DESC')->get_result();
$edit = null;
if ($action === 'edit' && $id) {
    $edit = query('SELECT * FROM users WHERE id=?', [$id])->get_result()->fetch_assoc();
}

include __DIR__ . '/includes/header.php';
?>

<h2 class="card-title">Users</h2>

<div class="grid" style="grid-template-columns:1.2fr 2fr;">
  <div>
    <div class="card">
      <div class="card-body">
        <h3 class="card-title" style="font-size:1.1rem;">Add User</h3>
        <form class="form" method="post" action="?action=create">
          <div class="field"><label class="label">Name</label><input class="input" name="name" required /></div>
          <div class="field"><label class="label">Email</label><input class="input" type="email" name="email" /></div>
          <div class="field"><label class="label">Username</label><input class="input" name="username" required /></div>
          <div class="field"><label class="label">Password</label><input class="input" type="password" name="password" required /></div>
          <div class="field">
            <label class="label">Role</label>
            <select class="select" name="role">
              <option value="staff">Staff</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button class="btn primary" type="submit">Create</button>
        </form>
      </div>
    </div>

    <?php if ($edit): ?>
    <div class="card" style="margin-top:1rem;">
      <div class="card-body">
        <h3 class="card-title" style="font-size:1.1rem;">Edit User</h3>
        <form class="form" method="post" action="?action=update&id=<?= esc($edit['id']) ?>">
          <div class="field"><label class="label">Name</label><input class="input" name="name" value="<?= esc($edit['name']) ?>" required /></div>
          <div class="field"><label class="label">Email</label><input class="input" type="email" name="email" value="<?= esc($edit['email']) ?>" /></div>
          <div class="field"><label class="label">New Password (optional)</label><input class="input" type="password" name="password" /></div>
          <div class="field">
            <label class="label">Role</label>
            <select class="select" name="role">
              <option value="staff" <?= $edit['role']==='staff'? 'selected':'' ?>>Staff</option>
              <option value="admin" <?= $edit['role']==='admin'? 'selected':'' ?>>Admin</option>
            </select>
          </div>
          <div class="field"><label><input type="checkbox" name="is_active" <?= $edit['is_active']? 'checked':'' ?> /> Active</label></div>
          <button class="btn primary" type="submit">Update</button>
        </form>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div>
    <div class="card">
      <div class="card-body">
        <h3 class="card-title" style="font-size:1.1rem;">All Users</h3>
        <table class="table">
          <thead><tr><th>ID</th><th>Name</th><th>Username</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php while($u = $users->fetch_assoc()): ?>
            <tr>
              <td><?= esc($u['id']) ?></td>
              <td><?= esc($u['name']) ?></td>
              <td><?= esc($u['username']) ?></td>
              <td><?= esc($u['role']) ?></td>
              <td><?= $u['is_active']? '<span class="badge green">Active</span>':'<span class="badge red">Inactive</span>' ?></td>
              <td>
                <a class="btn" href="?action=edit&id=<?= esc($u['id']) ?>">Edit</a>
                <?php if ((int)$u['id'] !== (int)current_user()['id']): ?>
                <a class="btn danger" href="?action=delete&id=<?= esc($u['id']) ?>" onclick="return confirm('Delete this user?');">Delete</a>
                <?php endif; ?>
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
