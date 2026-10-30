<?php
require_once __DIR__.'/config.php';
$tables=[];
try {
  $tables=$pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
  $hasProducts=in_array('products',$tables,true);
} catch(Throwable $e) {
  $hasProducts=false;
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="icon" href="assets/images/favicon.ico" type="image/x-icon"><link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32.png"><link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png"><title>Cek Database AFA</title>
<style>body{font-family:Arial;background:#f8f6f1;padding:24px;color:#171717}.box{max-width:720px;margin:auto;background:white;padding:28px;border:1px solid #ddd}li{margin:8px 0}.ok{color:#147a3d}.bad{color:#9a2222}code{background:#eee;padding:3px 6px}</style></head><body><div class="box"><h1>Database Check</h1>
<ul>
<li class="<?= $pdo?'ok':'bad' ?>">Koneksi MySQL: <?= $pdo?'BERHASIL':'GAGAL' ?></li>
<li class="<?= $hasProducts?'ok':'bad' ?>">Tabel <code>products</code>: <?= $hasProducts?'ADA':'BELUM ADA' ?></li>
<li>Database: <code>afacipta_karyanusantarakatalog</code></li>
</ul>
<?php if($hasProducts): ?><p class="ok"><b>Database siap digunakan.</b> Katalog dan menu admin seharusnya dapat berjalan.</p><p><a href="admin/">Buka menu upload produk</a> · <a href="katalog.php">Buka katalog</a></p><?php else: ?><p>Import <code>database/katalog.sql</code> ke database tersebut melalui phpMyAdmin.</p><?php endif; ?>
<p><b>Catatan keamanan:</b> hapus file <code>cek_database.php</code> setelah selesai pengecekan.</p></div></body></html>