<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!is_post()) { redirect('/public/submit.php'); }

$title = trim($_POST['title'] ?? '');
$category_name = trim($_POST['category_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$location_found = trim($_POST['location_found'] ?? '');
$date_found = trim($_POST['date_found'] ?? '');
$finder_name = trim($_POST['finder_name'] ?? '');
$finder_contact = trim($_POST['finder_contact'] ?? '');

// Resolve category by name (create if not exists and active)
$cat_id = null;
if ($category_name !== '') {
    $row = query('SELECT id FROM categories WHERE name=? LIMIT 1', [$category_name])->get_result()->fetch_assoc();
    if ($row) { $cat_id = (int)$row['id']; }
    else {
        query('INSERT INTO categories(name,description,is_active) VALUES(?, ?, 1)', [$category_name, 'Created from public submission']);
        $cat_id = db()->insert_id;
    }
}

// Upload file if provided
$photoPath = null;
$uploadDir = __DIR__ . '/../uploads';
if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
if (!empty($_FILES['photo']['name'])) {
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];
    if (in_array($ext, $allowed)) {
        $name = 'pub_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
        $dest = $uploadDir . '/' . $name;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
            $photoPath = 'uploads/' . $name;
        }
    }
}

if ($title && $cat_id && $finder_name && $finder_contact) {
    query('INSERT INTO items (category_id,title,description,photo, finder_name,finder_contact,location_found,date_found,status) VALUES (?,?,?,?,?,?,?,?,\'pending\')',
        [$cat_id,$title,$description,$photoPath,$finder_name,$finder_contact,$location_found,$date_found]);
}

redirect('/public/submit.php?ok=1');
