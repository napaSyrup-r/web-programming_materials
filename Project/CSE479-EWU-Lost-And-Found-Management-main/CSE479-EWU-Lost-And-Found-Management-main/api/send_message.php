<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!is_post()) { redirect('/public/contact.php'); }

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name && $email && $message) {
    query('INSERT INTO messages(name,email,subject,message) VALUES (?,?,?,?)', [$name,$email,$subject,$message]);
}

redirect('/public/contact.php?ok=1');
