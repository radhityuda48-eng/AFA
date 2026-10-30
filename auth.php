<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('AFA_INTERNAL');
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params(['httponly'=>true, 'secure'=>$secure, 'samesite'=>'Lax', 'path'=>'/']);
    session_start();
}
$authLocal = __DIR__ . '/config.local.php';
if (is_file($authLocal)) require_once $authLocal;
function afa_internal_logged_in() {
    return in_array($_SESSION['afa_internal_role'] ?? '', ['admin', 'sales'], true);
}
function afa_internal_role() { return $_SESSION['afa_internal_role'] ?? ''; }
function afa_require_internal() {
    if (!afa_internal_logged_in()) {
        $return = urlencode($_SERVER['REQUEST_URI'] ?? 'admin/index.php');
        $prefix = (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') !== false) ? '../' : '';
        header('Location: '.$prefix.'login.php?return='.$return); exit;
    }
}
function afa_require_admin() {
    afa_require_internal();
    if (afa_internal_role() !== 'admin') { http_response_code(403); exit('Akses ditolak.'); }
}
?>
