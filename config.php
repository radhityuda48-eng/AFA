<?php
/* AFA configuration loader.
 * Copy config.local.php.example to config.local.php and fill credentials there,
 * or set the corresponding environment variables in your hosting panel.
 * Never upload config.local.php to a public repository.
 */
$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) require_once $localConfig;
$db_host = getenv('AFA_DB_HOST') ?: ($AFA_DB_HOST ?? 'localhost');
$db_name = getenv('AFA_DB_NAME') ?: ($AFA_DB_NAME ?? '');
$db_user = getenv('AFA_DB_USER') ?: ($AFA_DB_USER ?? '');
$db_pass = getenv('AFA_DB_PASS') ?: ($AFA_DB_PASS ?? '');
if ($db_name === '' || $db_user === '') {
    http_response_code(500);
    exit('Konfigurasi database AFA belum diatur. Salin config.local.php.example menjadi config.local.php dan isi kredensial database.');
}
try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    error_log('AFA database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Database AFA belum dapat dihubungkan. Periksa pengaturan database di hosting.');
}
function product_image_url($value) {
    $value = trim((string)$value);
    if ($value === '') return '';
    $value = str_replace('\\', '/', $value);
    $value = preg_replace('~[?#].*$~', '', $value);
    $value = rawurldecode($value);
    $name = basename($value);
    if ($name === '' || $name === '.' || $name === '..') return '';
    $disk = __DIR__ . '/uploads/' . $name;
    if (!is_file($disk)) return '';
    return 'uploads/' . rawurlencode($name);
}
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function get_products($pdo,$limit=null){
    $sql="SELECT * FROM products WHERE active=1 ORDER BY id DESC";
    if($limit!==null) $sql.=" LIMIT ".(int)$limit;
    return $pdo->query($sql)->fetchAll();
}
function get_product($pdo,$id){
    $s=$pdo->prepare("SELECT * FROM products WHERE id=? AND active=1");
    $s->execute([$id]); return $s->fetch();
}
function get_categories($pdo){
    return $pdo->query("SELECT DISTINCT category FROM products WHERE active=1 AND category<>'' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
}
function get_brands($pdo){
    return $pdo->query("SELECT DISTINCT brand FROM products WHERE active=1 AND brand<>'' ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);
}
?>
