<?php
require_once __DIR__.'/auth.php';
$error='';
$return=(string)($_POST['return']??$_GET['return']??'admin/index.php');
if (preg_match('~^(?:https?:)?//~i',$return) || strpos($return,'\\')!==false) $return='admin/index.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $pass=(string)($_POST['password']??'');
  $role='';
  $adminHash = $AFA_ADMIN_PASSWORD_HASH ?? '';
  $salesHash = $AFA_SALES_PASSWORD_HASH ?? '';
  if ($adminHash && password_verify($pass, $adminHash)) $role='admin';
  elseif ($salesHash && password_verify($pass, $salesHash)) $role='sales';
  if ($role!=='') {
    session_regenerate_id(true);
    $_SESSION['afa_internal_role']=$role;
    $_SESSION['afa_internal_ok']=true;
    header('Location: '.$return); exit;
  }
  $error='Kata sandi tidak sesuai atau hash password belum dikonfigurasi di config.local.php.';
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="manifest" href="/manifest.webmanifest"><meta name="theme-color" content="#151515"><title>Akses Internal AFA</title><link rel="stylesheet" href="assets/css/style.css?v=secure-price"></head><body>
<div class="ticker"><div class="ticker-track"><span>WELCOME TO AFA CIPTA KARYA NUSANTARA</span><span>WELCOME TO AFA CIPTA KARYA NUSANTARA</span></div></div>
<main class="container" style="max-width:520px;padding:64px 18px"><section class="form-card"><p class="eyebrow">INTERNAL ACCESS</p><h1 style="font-family:Georgia,serif">AFA Sales & Admin</h1><p class="muted">Login diperlukan untuk melihat harga internal dan mencetak PDF berharga.</p>
<?php if($error):?><div class="error"><?=htmlspecialchars($error,ENT_QUOTES,'UTF-8')?></div><?php endif;?>
<form method="post"><input type="hidden" name="return" value="<?=htmlspecialchars($return,ENT_QUOTES,'UTF-8')?>"><label>Kata sandi internal<input type="password" name="password" required autocomplete="current-password"></label><button class="btn primary" type="submit">Masuk</button></form>
<p class="muted" style="font-size:12px">Katalog customer dapat dibuka tanpa login dan tidak menampilkan harga.</p></section></main><script src="/assets/js/pwa-register.js"></script></body></html>