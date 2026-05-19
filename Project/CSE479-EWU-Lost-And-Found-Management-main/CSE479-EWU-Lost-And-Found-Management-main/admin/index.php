<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!empty($_SESSION['user'])) {
    redirect('/admin/dashboard.php');
}

$error = '';
if (is_post()) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = query('SELECT id,name,email,username,password,role,is_active FROM users WHERE username=? LIMIT 1', [$username]);
    $user = $stmt->get_result()->fetch_assoc();
    $isActive = $user && (int)$user['is_active'] === 1;
    $isValid = $user && (
      // Support both hashed and legacy plaintext for backward compatibility
      password_verify($password, (string)$user['password']) || (string)$user['password'] === (string)$password
    );
    if ($isActive && $isValid) {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];
        redirect('/admin/dashboard.php');
    } else {
      // Diagnostic details to help troubleshooting
      $exists = $user ? 'yes' : 'no';
      $error = 'Invalid credentials or account disabled. (user exists: ' . $exists . ')';
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login · Lost & Found</title>
    <link rel="stylesheet" href="/assets/css/styles.css" />
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico" />
    <link rel="icon" type="image/png" href="/assets/img/logo.png" />
  </head>
  <body>
    <main class="container" style="max-width: 420px; padding-top: 8vh;">
      <div class="slider" style="margin-bottom: 1rem;">
        <div class="slide">Welcome Back</div>
        <div class="slide">Admin Portal</div>
        <div class="slide">Manage Items</div>
      </div>
      <div class="card">
        <div class="card-body">
          <h2 class="card-title">Admin Login</h2>
          <?php if ($error): ?>
          <div class="card" style="margin: 0.5rem 0;">
            <div class="card-body">
              <div class="error"><?= esc($error) ?></div>
              <?php if (isset($_POST['username'])): ?>
                <div class="muted">Submitted username: <?= esc($_POST['username']) ?></div>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
          <form class="form" method="post" action="/admin/index.php" autocomplete="off">
            <div class="field">
              <label class="label">Username</label>
              <input class="input" type="text" name="username" required />
            </div>
            <div class="field">
              <label class="label">Password</label>
              <input class="input" type="password" name="password" required />
            </div>
            <div>
              <button class="btn primary" type="submit">Login</button>
            </div>
          </form>
        </div>
      </div>
      <p class="muted" style="margin-top: 0.75rem;">Tip: Use a hashed password in the database (generated via PHP <code>password_hash()</code>). You can temporarily use <strong>admin/admin123</strong> for legacy plaintext.</p>
    </main>
    <footer class="app-footer">
      <div class="container"><img class="logo-small" src="/assets/img/logo.png" alt="Logo" /> © <?= date('Y') ?> <span>Azizul Abedin Azmi</span> · Admin</div>
    </footer>
  </body>
</html>
