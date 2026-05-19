<?php
// Minimal MySQLi connection helper (beginner-friendly)
// Adjust credentials if your XAMPP setup differs.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lf_db');

function db(): mysqli {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

function query(string $sql, array $params = []): mysqli_stmt {
    $stmt = db()->prepare($sql);
    if (!$stmt) {
        die('SQL prepare failed: ' . db()->error);
    }
    if (!empty($params)) {
        // Infer simple types (s for string, i for int)
        $types = '';
        $bind = [];
        foreach ($params as $p) {
            if (is_int($p)) { $types .= 'i'; }
            elseif (is_double($p) || is_float($p)) { $types .= 'd'; }
            else { $types .= 's'; }
            $bind[] = $p;
        }
        $stmt->bind_param($types, ...$bind);
    }
    if (!$stmt->execute()) {
        die('SQL execute failed: ' . $stmt->error);
    }
    return $stmt;
}

?>
