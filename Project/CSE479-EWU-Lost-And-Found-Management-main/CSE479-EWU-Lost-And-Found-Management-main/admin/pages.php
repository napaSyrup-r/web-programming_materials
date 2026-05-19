<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$slug = $_GET['slug'] ?? 'home';

// Only admins can edit Terms & Privacy
if (in_array($slug, ['terms','privacy'], true)) {
  require_role('admin');
}

if (is_post()) {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content_html'] ?? '';
    $exists = query('SELECT id FROM pages WHERE slug=?', [$slug])->get_result()->fetch_assoc();
    if ($exists) {
        query('UPDATE pages SET title=?, content_html=? WHERE slug=?', [$title,$content,$slug]);
        flash_set('ok','Page updated.');
    } else {
        query('INSERT INTO pages(slug,title,content_html) VALUES(?,?,?)', [$slug,$title,$content]);
        flash_set('ok','Page created.');
    }
    redirect('/admin/pages.php?slug=' . urlencode($slug));
}

$page = query('SELECT * FROM pages WHERE slug=?', [$slug])->get_result()->fetch_assoc();

include __DIR__ . '/includes/header.php';
?>

<h2 class="card-title">Page Management</h2>
<div class="grid" style="grid-template-columns: 220px 1fr;">
  <aside>
    <div class="card"><div class="card-body">
      <div class="field"><a class="btn" href="?slug=home">Home</a></div>
      <div class="field"><a class="btn" href="?slug=about">About</a></div>
      <div class="field"><a class="btn" href="?slug=contact">Contact</a></div>
      <div class="field"><a class="btn" href="?slug=terms">Terms</a></div>
      <div class="field"><a class="btn" href="?slug=privacy">Privacy</a></div>
    </div></div>
  </aside>
  <section>
    <div class="card"><div class="card-body">
      <h3 class="card-title" style="font-size:1.1rem;">Edit: <?= esc(strtoupper($slug)) ?></h3>
      <form class="form" method="post" action="?slug=<?= esc($slug) ?>">
        <div class="field"><label class="label">Title</label><input class="input" name="title" value="<?= esc($page['title'] ?? '') ?>" required /></div>
        <div class="field"><label class="label">Content (HTML allowed)</label><textarea class="textarea" name="content_html" rows="10"><?= esc($page['content_html'] ?? '') ?></textarea></div>
        <button class="btn primary" type="submit">Save</button>
      </form>
    </div></div>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
