<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function esc(?string $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function is_post(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }

function flash_set(string $key, string $msg): void {
    $_SESSION['flash'][$key] = $msg;
}

function flash_get(string $key): ?string {
    if (!empty($_SESSION['flash'][$key])) {
        $m = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $m;
    }
    return null;
}

// Settings helpers
function get_setting(string $key, string $default = ''): string {
    require_once __DIR__ . '/db.php';
    $row = query('SELECT svalue FROM settings WHERE skey=?', [$key])->get_result()->fetch_assoc();
    return $row['svalue'] ?? $default;
}

function set_setting(string $key, string $value): void {
    require_once __DIR__ . '/db.php';
    // Upsert-like behavior
    $exists = query('SELECT id FROM settings WHERE skey=?', [$key])->get_result()->fetch_assoc();
    if ($exists) {
        query('UPDATE settings SET svalue=? WHERE skey=?', [$value, $key]);
    } else {
        query('INSERT INTO settings(skey,svalue) VALUES(?,?)', [$key, $value]);
    }
}

function is_service_off(): bool {
    return strtolower(get_setting('service_status', 'on')) === 'off';
}

function service_reason(): string {
    return get_setting('service_reason', 'Service is temporarily unavailable.');
}

// Enforce service mode: blocks public access when OFF; admins can still access admin to toggle back.
function enforce_service_mode_or_show(): void {
    if (!is_service_off()) { return; }
    // Render a simple full-page notice; used on public pages
    http_response_code(503);
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8" />'
       . '<meta name="viewport" content="width=device-width, initial-scale=1" />'
       . '<title>Service Unavailable</title>'
       . '<link rel="stylesheet" href="/assets/css/styles.css" /></head><body>'
       . '<main class="container" style="padding-top:8vh;max-width:760px;">'
       . '<div class="card"><div class="card-body">'
       . '<h2 class="card-title">Service Temporarily Offline</h2>'
       . '<div class="muted">' . esc(service_reason()) . '</div>'
       . '<div class="muted" style="margin-top:0.8rem;">Please check back later.</div>'
       . '</div></div>'
       . '</main><footer class="app-footer"><div class="container">© ' . date('Y') . ' Lost & Found</div></footer>'
       . '</body></html>';
    exit;
}

// ---------- Error pages ----------
function render_error_page(int $code, string $title, string $message): void {
    http_response_code($code);
    $year = date('Y');
     echo '<!doctype html><html lang="en"><head><meta charset="utf-8" />'
       . '<meta name="viewport" content="width=device-width, initial-scale=1" />'
       . '<title>' . esc($code . ' ' . $title) . ' · Lost & Found</title>'
       . '<link rel="stylesheet" href="/assets/css/styles.css" />'
         . '<link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico" />'
         . '<link rel="icon" type="image/png" href="/assets/img/logo.png" />'
       . '<style>'
       . '.error-hero{position:relative;display:grid;place-items:center;min-height:70vh;}'
       . '.error-blob{position:absolute;inset:-10%;background:radial-gradient(800px 400px at 30% 20%,rgba(96,165,250,0.18),transparent),'
       . 'radial-gradient(800px 400px at 70% 80%,rgba(99,102,241,0.18),transparent);filter:blur(20px);animation:floaty 12s ease-in-out infinite;pointer-events:none;}'
       . '.error-title{font-size: clamp(2.5rem,8vw,5rem);font-weight:800;letter-spacing:1px;color:#fff;text-align:center;}'
       . '.error-sub{color:#cbd5e1;text-align:center;max-width:760px;margin:0.75rem auto 0;}'
       . '.error-actions{display:flex;gap:.6rem;justify-content:center;margin-top:1rem;}'
       . '@keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-16px)}}'
       . '</style></head><body>'
       . '<div class="error-hero container">'
       . '<div class="error-blob"></div>'
       . '<div style="position:relative;z-index:1;width:100%">'
       . '<div class="card"><div class="card-body" style="text-align:center">'
       . '<div class="error-title">' . esc((string)$code) . ' · ' . esc($title) . '</div>'
       . '<div class="error-sub">' . $message . '</div>'
       . '<div class="error-actions">'
       . '<a class="btn" href="/">Home</a>'
       . '<a class="btn" href="javascript:history.back()">Go Back</a>'
       . '</div>'
       . '</div></div>'
       . '</div></div>'
       . '<footer class="app-footer"><div class="container">© ' . $year . ' Lost & Found</div></footer>'
       . '</body></html>';
    exit;
}

function render_forbidden(): void {
    render_error_page(403, 'Forbidden', 'You do not have permission to access this resource. If you believe this is an error, please contact an administrator.');
}

function render_not_found(): void {
    render_error_page(404, 'Not Found', 'The page you are looking for could not be found or may have been moved.');
}

// ---------- Notifications ----------
function notify_admins(string $subject, string $message): void {
    require_once __DIR__ . '/db.php';
    $rs = query("SELECT email FROM users WHERE role='admin' AND is_active=1 AND email IS NOT NULL AND email <> ''")->get_result();
    $recipients = [];
    while($row = $rs->fetch_assoc()) { $recipients[] = $row['email']; }
    if (empty($recipients)) { $recipients = ['admin@example.com']; }

    $headers = "Content-Type: text/plain; charset=UTF-8\r\n";
    foreach ($recipients as $to) {
        $sent = @mail($to, $subject, $message, $headers);
        if (!$sent) {
            // Fallback to simple log file for local dev
            $logDir = __DIR__ . '/../uploads/logs';
            if (!is_dir($logDir)) { @mkdir($logDir, 0777, true); }
            @file_put_contents($logDir . '/mail.log', date('c') . " TO:" . $to . " SUBJECT:" . $subject . "\n" . $message . "\n\n", FILE_APPEND);
        }
    }
}

?>
