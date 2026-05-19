<?php
// Simple CLI/web script to generate a password hash for seeding
// Usage (CLI): php tools/hash_password.php yourPassword
// Usage (web): visit /tools/hash_password.php?p=yourPassword

$p = null;
if (PHP_SAPI === 'cli') {
    $p = $argv[1] ?? null;
} else {
    $p = $_GET['p'] ?? null;
}

if (!$p) {
    echo "Usage: php tools/hash_password.php <password>\n";
    echo "Or open /tools/hash_password.php?p=<password> in your browser\n";
    exit(1);
}

$hash = password_hash($p, PASSWORD_DEFAULT);
echo "Password: $p\n";
echo "Hash: $hash\n";
