<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
require_role('admin');

if (is_post()) {
  $site = trim($_POST['site_name'] ?? '');
  $org = trim($_POST['org_name'] ?? '');
  $service = trim($_POST['service_status'] ?? 'on');
  $reason = trim($_POST['service_reason'] ?? '');
  if ($site !== '') { query('INSERT INTO settings(skey,svalue) VALUES(\'site_name\',?) ON DUPLICATE KEY UPDATE svalue=VALUES(svalue)', [$site]); }
  if ($org !== '') { query('INSERT INTO settings(skey,svalue) VALUES(\'org_name\',?) ON DUPLICATE KEY UPDATE svalue=VALUES(svalue)', [$org]); }
  if ($service !== '') { query('INSERT INTO settings(skey,svalue) VALUES(\'service_status\',?) ON DUPLICATE KEY UPDATE svalue=VALUES(svalue)', [$service]); }
  query('INSERT INTO settings(skey,svalue) VALUES(\'service_reason\',?) ON DUPLICATE KEY UPDATE svalue=VALUES(svalue)', [$reason]);
  flash_set('ok','Settings updated.');
  redirect('/admin/settings.php');
}

function setting($key) {
  $row = query('SELECT svalue FROM settings WHERE skey=?', [$key])->get_result()->fetch_assoc();
  return $row['svalue'] ?? '';
}

include __DIR__ . '/includes/header.php';
?>

<h2 class="card-title">System Settings</h2>
<div class="card" style="margin-top:1rem;">
  <div class="card-body">
    <form class="form" method="post">
      <div class="field"><label class="label">Site Name</label><input class="input" name="site_name" value="<?= esc(setting('site_name')) ?>" required /></div>
      <div class="field"><label class="label">Organization Name</label><input class="input" name="org_name" value="<?= esc(setting('org_name')) ?>" /></div>
      <div class="grid" style="grid-template-columns:1fr 2fr; gap:0.6rem;">
        <div class="field">
          <label class="label">Service Status</label>
          <label class="btn" style="user-select:none; display:inline-flex; align-items:center; gap:0.5rem;">
            <input type="radio" name="service_status" value="on" <?= setting('service_status')==='off' ? '' : 'checked' ?> />
            <span>Service ON</span>
          </label>
          <label class="btn danger" style="user-select:none; display:inline-flex; align-items:center; gap:0.5rem; margin-left:0.4rem;">
            <input type="radio" name="service_status" value="off" <?= setting('service_status')==='off' ? 'checked' : '' ?> />
            <span>Service OFF</span>
          </label>
          <div class="help">Toggle to disable public access.</div>
        </div>
        <div class="field">
          <label class="label">Reason (shown publicly when OFF)</label>
          <textarea class="textarea" name="service_reason" rows="4" placeholder="e.g., Maintenance until 5 PM"><?= esc(setting('service_reason')) ?></textarea>
        </div>
      </div>
      <button class="btn primary" type="submit">Save</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
