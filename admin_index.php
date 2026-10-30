<?php
require_once __DIR__.'/../config.php';
require_once __DIR__.'/../auth.php';
afa_require_internal();
$msg='';$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(isset($_POST['delete'])){
  $id=(int)$_POST['delete'];$p=get_product($pdo,$id);
  if($p&&$p['image']&&is_file(__DIR__.'/../uploads/'.$p['image']))@unlink(__DIR__.'/../uploads/'.$p['image']);
  $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);$msg='Produk berhasil dihapus.';
 }
 if(isset($_POST['save'])){
  $image='';
  if(!isset($_FILES['image']) || $_FILES['image']['error']===UPLOAD_ERR_NO_FILE) {
   $err='Foto produk wajib dipilih sebelum menyimpan.';
  } elseif($_FILES['image']['error']!==UPLOAD_ERR_OK) {
   if($_FILES['image']['error']===UPLOAD_ERR_INI_SIZE) { $err='Foto melewati batas upload_max_filesize pada hosting (PHP kode 1). Foto kini dikompres otomatis oleh website; bila masih terjadi, naikkan upload_max_filesize dan post_max_size di cPanel → MultiPHP INI Editor atau hubungi hosting.'; } elseif($_FILES['image']['error']===UPLOAD_ERR_FORM_SIZE) { $err='Foto melebihi batas ukuran formulir. Pilih foto yang lebih kecil.'; } elseif($_FILES['image']['error']===UPLOAD_ERR_PARTIAL) { $err='Foto hanya terunggah sebagian. Periksa koneksi internet lalu coba kembali.'; } elseif($_FILES['image']['error']===UPLOAD_ERR_NO_TMP_DIR) { $err='Folder sementara upload PHP tidak tersedia. Hubungi penyedia hosting.'; } elseif($_FILES['image']['error']===UPLOAD_ERR_CANT_WRITE) { $err='Hosting gagal menulis file upload ke disk. Periksa kapasitas hosting atau hubungi penyedia hosting.'; } else { $err='Upload foto gagal (kode '.$_FILES['image']['error'].'). Coba ulangi dan periksa konfigurasi upload hosting.'; }
  } else {
   $tmp=$_FILES['image']['tmp_name'];
   $ext=strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));
   $allowed=['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp'];
   $mime='';
   if(function_exists('finfo_open')) {
    $fi=finfo_open(FILEINFO_MIME_TYPE);
    if($fi){$mime=finfo_file($fi,$tmp) ?: '';finfo_close($fi);}
   }
   $validImage=@getimagesize($tmp);
   if(!isset($allowed[$ext]) || !$validImage || ($mime!=='' && $mime!==$allowed[$ext])) {
    $err='File harus berupa gambar JPG, JPEG, PNG, atau WEBP yang valid.';
   } elseif($_FILES['image']['size']>10*1024*1024) {
    $err='Ukuran foto maksimal 10 MB.';
   } else {
    $uploadDir=__DIR__.'/../uploads/';
    if(!is_dir($uploadDir)) @mkdir($uploadDir,0755,true);
    if(!is_dir($uploadDir) || !is_writable($uploadDir)) {
     $err='Folder uploads tidak dapat ditulis. Periksa permission folder uploads di hosting.';
    } else {
     $image='p_'.bin2hex(random_bytes(12)).'.'.$ext;
     if(!move_uploaded_file($_FILES['image']['tmp_name'],$uploadDir.$image)) {
      $err='Server gagal menyimpan gambar ke folder uploads.';
     }
    }
   }
  }
  if(!$err){
   $s=$pdo->prepare("INSERT INTO products(name,sku,brand,category,description,specification,hpp,harga1,harga2,harga3,image,active) VALUES(?,?,?,?,?,?,?,?,?,?,?,1)");
   $s->execute([trim($_POST['name']),trim($_POST['sku']),trim($_POST['brand']),trim($_POST['category']),trim($_POST['description']),trim($_POST['specification']),(float)$_POST['hpp'],(float)$_POST['harga1'],(float)$_POST['harga2'],(float)$_POST['harga3'],$image]);
   $msg='Produk berhasil disimpan ke database.';
  }
 }
}
$products=$pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
$categories=$pdo->query("SELECT DISTINCT category FROM products WHERE category<>'' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
function money($n){return 'Rp '.number_format((float)$n,0,',','.');}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon"><link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32.png"><link rel="apple-touch-icon" href="../assets/images/apple-touch-icon.png">
<title>Kelola Produk — AFA Cipta Karya Nusantara</title><link rel="stylesheet" href="../assets/css/style.css?v=logo-final"></head><body>
<div class="ticker"><div class="ticker-track"><span>WELCOME TO AFA CIPTA KARYA NUSANTARA</span><span>WELCOME TO AFA CIPTA KARYA NUSANTARA</span><span>WELCOME TO AFA CIPTA KARYA NUSANTARA</span></div></div>
<main class="container admin-wrap"><div class="admin-head"><div><p class="eyebrow">CATALOG MANAGEMENT</p><h1>Kelola Produk</h1><p class="muted">Kelola foto, produk, HPP dan tiga tingkat harga dalam satu katalog.</p></div><div style="display:flex;gap:9px;flex-wrap:wrap"><a class="btn ghost" href="../katalog.php">Buka Katalog</a><a class="btn ghost" href="../logout.php">Keluar</a><a class="btn primary" href="../print.php?category=&price=harga1" target="_blank">Cetak PDF</a></div></div>
<?php if($msg):?><div class="notice"><?=e($msg)?></div><?php endif;?><?php if($err):?><div class="error"><?=e($err)?></div><?php endif;?>
<form class="form-card" method="post" enctype="multipart/form-data"><input type="hidden" name="save" value="1"><h2>Tambah Produk</h2>
<div class="form-grid"><label class="image-upload-field">Foto Produk<div class="upload-box"><input id="imageInput" type="file" name="image" accept="image/*" capture="environment"><label for="imageInput" class="upload-button">📷 PILIH / AMBIL FOTO PRODUK</label><div id="uploadStatus" class="upload-status">Belum ada foto dipilih</div><div id="imagePreview" class="image-preview"><div class="preview-placeholder"><span>PREVIEW FOTO</span><small>Pilih foto untuk melihat wujud produk di sini sebelum disimpan.</small></div></div><button type="button" id="removePreview" class="remove-preview" hidden>✕ Ganti / Hapus Foto</button></div></label>
<label>Nama Produk<input required name="name"></label><label>Kode / SKU<input name="sku"></label><label>Brand<input name="brand"></label><label>Kategori<input name="category" placeholder="Contoh: Mouse"></label><label>HPP<input type="number" step="0.01" name="hpp"></label><label>Harga 1<input type="number" step="0.01" name="harga1"></label><label>Harga 2<input type="number" step="0.01" name="harga2"></label><label>Harga 3<input type="number" step="0.01" name="harga3"></label></div>
<label>Deskripsi<textarea name="description"></textarea></label><label>Spesifikasi<textarea name="specification"></textarea></label><button class="btn primary" type="submit">Simpan Produk</button></form>

<div class="form-card"><h2>Cetak Katalog PDF</h2><p class="muted">Pilih kategori dan tingkat harga. Hasil cetak hanya memuat produk sesuai filter tersebut.</p>
<div class="print-filter-grid"><label>Kategori<select id="printCategory"><option value="">Semua Kategori</option><?php foreach($categories as $c):?><option value="<?=e($c)?>"><?=e($c)?></option><?php endforeach;?></select></label>
<label>Harga yang dicetak<select id="printPrice"><option value="harga1">Harga 1</option><option value="harga2">Harga 2</option><option value="harga3">Harga 3</option><option value="hpp">HPP (Internal)</option></select></label>
<label class="print-action-label">PDF Internal (harga terpilih)<a id="printBtn" class="btn primary print-btn" target="_blank" href="../print.php?price=harga1">🖨 Cetak PDF Internal</a></label>
<label class="print-action-label">PDF Customer (tanpa harga)<a id="customerPrintBtn" class="btn ghost print-btn" target="_blank" href="../customer-print.php">Cetak PDF Customer</a></label></div></div>

<div class="table-card"><h2>Produk Tersimpan</h2><table><tr><th>Foto</th><th>Produk</th><th>SKU</th><th>Kategori</th><th>HPP</th><th>Harga 1</th><th>Harga 2</th><th>Harga 3</th><th>Aksi</th></tr>
<?php foreach($products as $p):?><tr><td><?php $imageUrl=product_image_url($p['image']); if($imageUrl):?><img class="thumb" src="../<?=e($imageUrl)?>" alt="<?=e($p['name'])?>" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><span hidden>Foto hilang</span><?php else:?><span class="muted">Belum ada foto</span><?php endif;?></td><td><?=e($p['name'])?></td><td><?=e($p['sku'])?></td><td><?=e($p['category'])?></td><td><?=money($p['hpp'])?></td><td><?=money($p['harga1'])?></td><td><?=money($p['harga2'])?></td><td><?=money($p['harga3'])?></td><td><form method="post" onsubmit="return confirm('Hapus produk ini?')"><button class="delete" name="delete" value="<?=$p['id']?>">Hapus</button></form></td></tr><?php endforeach;?></table></div>
</main><script src="../assets/js/admin.js?v=3"></script><script>
(function(){var c=document.getElementById('printCategory'),p=document.getElementById('printPrice'),b=document.getElementById('printBtn'),cb=document.getElementById('customerPrintBtn');function u(){b.href='../print.php?category='+encodeURIComponent(c.value)+'&price='+encodeURIComponent(p.value);cb.href='../customer-print.php?category='+encodeURIComponent(c.value);}c.addEventListener('change',u);p.addEventListener('change',u);u();})();
</script></body></html>