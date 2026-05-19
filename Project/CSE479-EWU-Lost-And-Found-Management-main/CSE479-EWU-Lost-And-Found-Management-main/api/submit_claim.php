<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/public/'); }

$item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$name = trim($_POST['claimant_name'] ?? '');
$contact = trim($_POST['claimant_contact'] ?? '');
$evidence = trim($_POST['evidence_text'] ?? '');
$filePath = null;

if (!$item_id || !$name || !$contact || !$evidence) {
  redirect('/public/item.php?id=' . $item_id);
}

// Upload optional file
if (!empty($_FILES['evidence_file']['name']) && is_uploaded_file($_FILES['evidence_file']['tmp_name'])) {
  $ext = strtolower(pathinfo($_FILES['evidence_file']['name'], PATHINFO_EXTENSION));
  $allowed = ['jpg','jpeg','png','gif','webp','pdf'];
  if (in_array($ext, $allowed, true)) {
    $dir = __DIR__ . '/../uploads/claims';
    if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
    $fn = 'claims/' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destRel = 'uploads/' . $fn;
    $destAbs = __DIR__ . '/../' . $destRel;
    if (!is_dir(dirname($destAbs))) { @mkdir(dirname($destAbs), 0777, true); }
    if (move_uploaded_file($_FILES['evidence_file']['tmp_name'], $destAbs)) {
      $filePath = $destRel;
    }
  }
}

query('INSERT INTO claims(item_id,claimant_name,claimant_contact,evidence_text,evidence_file) VALUES(?,?,?,?,?)', [
  $item_id, $name, $contact, $evidence, $filePath
]);

// Notify admins
$item = query('SELECT title FROM items WHERE id=?', [$item_id])->get_result()->fetch_assoc();
$subject = 'New Claim Request: ' . ($item['title'] ?? ('Item #' . $item_id));
$msg  = "A new claim request was submitted.\n\n";
$msg .= "Item: " . ($item['title'] ?? ('#' . $item_id)) . " (ID: $item_id)\n";
$msg .= "Claimant: $name\n";
$msg .= "Contact: $contact\n\n";
$msg .= "Evidence:\n$evidence\n\n";
if ($filePath) { $msg .= "Attachment: $filePath\n"; }
notify_admins($subject, $msg);

redirect('/public/item.php?id=' . $item_id . '&claimed=1');
