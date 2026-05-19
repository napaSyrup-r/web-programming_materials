<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function require_login(): void {
    if (empty($_SESSION['user'])) {
        header('Location: /admin/index.php');
        exit;
    }
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_role(string $role): void {
    $u = current_user();
    if (!$u || ($u['role'] !== $role && $u['role'] !== 'admin')) {
        require_once __DIR__ . '/../../includes/helpers.php';
        render_forbidden();
    }
}

?>
