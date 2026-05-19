<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$u = current_user();
$error = '';

// Ensure uploads dir exists
$uploadDir = __DIR__ . '/../uploads';
if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }

if (is_post()) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $avatarPath = null;

    if (!empty($_FILES['avatar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($ext, $allowed)) {
            $nameFile = 'avatar_' . $u['id'] . '_' . time() . '.' . $ext;
            $dest = $uploadDir . '/' . $nameFile;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                $avatarPath = 'uploads/' . $nameFile;
            }
        } else {
            $error = 'Invalid avatar file type.';
        }
    }

    if (!$error) {
        query('UPDATE users SET name=?, email=? WHERE id=?', [$name,$email,$u['id']]);
        if ($password !== '') {
            query('UPDATE users SET password=? WHERE id=?', [$password, $u['id']]);
        }
        if ($avatarPath) {
            query('UPDATE users SET avatar=? WHERE id=?', [$avatarPath, $u['id']]);
        }
        // refresh session
        $row = query('SELECT id,name,email,username,role,avatar FROM users WHERE id=?', [$u['id']])->get_result()->fetch_assoc();
        $_SESSION['user'] = $row;
        flash_set('ok','Account updated.');
        redirect('/admin/account.php');
    }
}

$row = query('SELECT * FROM users WHERE id=?', [$u['id']])->get_result()->fetch_assoc();

include __DIR__ . '/includes/header.php';
?>

<h2 class="card-title">My Account</h2>

<div class="grid" style="grid-template-columns: 260px 1fr; gap:1rem;">
  <div>
    <div class="card"><div class="card-body" style="text-align:center;">
      <?php if (!empty($row['avatar'])): ?>
        <img src="/<?= esc($row['avatar']) ?>" alt="avatar" style="width:140px;height:140px;object-fit:cover;border-radius:50%;border:2px solid rgba(148,163,184,0.3);" />
      <?php else: ?>
        <div style="width:140px;height:140px;border-radius:50%;background:rgba(148,163,184,0.2);display:grid;place-items:center;margin:0 auto;">👤</div>
      <?php endif; ?>
      <div class="muted" style="margin-top:0.6rem;"><?= esc($row['username']) ?> (<?= esc($row['role']) ?>)</div>
    </div></div>
  </div>
  <div>
    <div class="card"><div class="card-body">
      <?php if ($error): ?><div class="error" style="margin-bottom:0.6rem;"><?= esc($error) ?></div><?php endif; ?>
      <form class="form" method="post" enctype="multipart/form-data">
        <div class="grid" style="grid-template-columns:1fr 1fr; gap:0.6rem;">
          <div class="field"><label class="label">Name</label><input class="input" name="name" value="<?= esc($row['name']) ?>" required /></div>
          <div class="field"><label class="label">Email</label><input class="input" type="email" name="email" value="<?= esc($row['email']) ?>" /></div>
        </div>
        <div class="grid" style="grid-template-columns:1fr 1fr; gap:0.6rem;">
          <div class="field"><label class="label">New Password (optional)</label><input class="input" type="password" name="password" /></div>
          <div class="field"><label class="label">Avatar</label><input class="file" type="file" name="avatar" accept="image/*" /></div>
        </div>
        <button class="btn primary" type="submit">Save Changes</button>
      </form>
    </div></div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
