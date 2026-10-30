<?php
require_once __DIR__.'/config.php';
$category=trim($_GET['category']??'');
$price='harga1';
$allowed=['hpp'=>'HPP','harga1'=>'Harga 1','harga2'=>'Harga 2','harga3'=>'Harga 3'];
if(!isset($allowed[$price]))$price='harga1';
$sql="SELECT * FROM products WHERE active=1";$args=[];
if($category!==''){$sql.=" AND category=?";$args[]=$category;}
$sql.=" ORDER BY category,name";
$s=$pdo->prepare($sql);$s->execute($args);$products=$s->fetchAll();
function e2($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function money2($n){return 'Rp '.number_format((float)$n,0,',','.');}
$label='';
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Katalog Produk — <?=e2($category?:'Semua Kategori')?></title>
<style>
@page{size:A4;margin:10mm 9mm}
*{box-sizing:border-box}
:root{--gold:#d8b45d;--gold2:#f0d78c;--ink:#090909;--panel:#101010;--muted:#c7bda5}
body{margin:0;background:#e9e5dc;color:#f6f0df;font-family:Arial,Helvetica,sans-serif;-webkit-print-color-adjust:exact;print-color-adjust:exact}
.sheet{width:210mm;min-height:297mm;margin:16px auto;background:var(--ink);padding:10mm 10mm 8mm;box-shadow:0 12px 45px rgba(0,0,0,.2);position:relative;overflow:hidden}
.sheet:before{content:"";position:absolute;inset:0;pointer-events:none;background:radial-gradient(ellipse at 100% 0%,rgba(216,180,93,.10),transparent 36%),radial-gradient(ellipse at 0% 100%,rgba(216,180,93,.07),transparent 32%)}
.top{position:relative;background:#050505;color:#fff;padding:17px 19px;display:flex;justify-content:space-between;align-items:center;border:1px solid #8f6c27;border-bottom:3px solid var(--gold);min-height:72px}
.company{display:flex;gap:13px;align-items:center;min-width:0}.logo{width:67px;height:43px;object-fit:contain;background:#fff;padding:3px;border:1px solid #d9bd75}
.company h1{font:400 20px Georgia,'Times New Roman',serif;margin:0;color:var(--gold2);letter-spacing:.65px}.company p{font-size:7px;letter-spacing:3px;margin:6px 0 0;color:#c9c1ad}
.meta{text-align:right;font-size:8px;color:#e3d3a6;line-height:1.8;letter-spacing:.7px;text-transform:uppercase}
.title{position:relative;padding:22px 0 15px;border-bottom:1px solid #8f6c27}.eyebrow{font-size:8px;letter-spacing:3.5px;color:var(--gold);font-weight:700;text-transform:uppercase}.title h2{font:400 29px Georgia,'Times New Roman',serif;margin:7px 0 10px;color:#fff4d6}.chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}.chip{border:1px solid #94732f;background:#100f0c;padding:5px 10px;font-size:8px;color:#e5cc8c;letter-spacing:.3px}
.grid{position:relative;display:flex;flex-direction:column;gap:9px;margin-top:15px}
.card{border:1px solid #a17d32;background:linear-gradient(110deg,#111 0%,#080808 66%,#17130a 100%);break-inside:avoid;page-break-inside:avoid;display:grid;grid-template-columns:34mm minmax(0,1fr);min-height:37mm;position:relative;overflow:hidden}
.card:after{content:"";position:absolute;left:0;right:0;bottom:0;height:2px;background:linear-gradient(90deg,#8a6725,#f2d889,#8a6725);opacity:.85}
.photo{min-height:37mm;background:#f7f4ec;border-right:1px solid #9a782f;display:flex;align-items:center;justify-content:center;padding:5px;position:relative;overflow:hidden}.photo img{width:100%;height:100%;max-height:43mm;object-fit:contain;transform:scale(var(--afa-zoom,1));transform-origin:center center;transition:transform .15s ease}.noimg{font:italic 12px Georgia;color:#a88743;text-align:center}
.info{padding:11px 14px;display:flex;flex-direction:column;justify-content:center;min-width:0}.cat{font-size:7px;letter-spacing:2.4px;color:var(--gold);text-transform:uppercase;font-weight:700}.name{font:400 17px Georgia,'Times New Roman',serif;margin:6px 0 0;line-height:1.25;color:#fff1c8;overflow-wrap:anywhere}.sku{display:none}
.pricebox{border-left:1px solid #7e632c;margin:9px 0;padding:5px 12px;display:flex;flex-direction:column;justify-content:center;align-items:flex-start}.price-label{font-size:8px;letter-spacing:2.5px;color:#c7a34e;text-transform:uppercase;margin:0 0 7px}.price{font-size:18px;font-weight:700;line-height:1.15;color:#f4d67e;white-space:nowrap}
.foot{position:relative;margin-top:18px;padding:10px 0 0;border-top:1px solid #8f6c27;display:flex;justify-content:space-between;gap:10px;font-size:7px;color:#c8b57d;letter-spacing:1.3px;text-transform:uppercase}
.actions{position:sticky;top:0;z-index:3;background:#111;padding:10px;text-align:center}.zoom-tools{position:absolute;z-index:2;left:4px;top:4px;display:flex;gap:3px;background:rgba(8,8,8,.88);padding:3px;border:1px solid #b18a39;border-radius:5px}.zoom-tools button{border:1px solid #94732f;background:#17130a;color:#f3d47b;width:23px;height:23px;line-height:1;font-weight:bold;cursor:pointer;border-radius:3px;padding:0}.zoom-tools output{font-size:9px;color:#f4dfa4;min-width:28px;text-align:center;align-self:center}.actions button{background:#c8a35d;border:0;padding:10px 18px;font-weight:700;cursor:pointer}.actions a{color:#fff;font-size:11px;margin-left:12px}
@media print{.zoom-tools{display:none!important}body{background:#fff}.sheet{width:auto;min-height:auto;margin:0;box-shadow:none;padding:0;background:#090909}.actions{display:none}.top,.card,.photo,.pricebox{-webkit-print-color-adjust:exact;print-color-adjust:exact}.grid{gap:7px}.card{min-height:34mm}.photo{min-height:34mm}.photo img{max-height:39mm}.name{font-size:15px}.price{font-size:16px}.foot{margin-top:14px}}
@media(max-width:700px){.sheet{width:100%;min-height:auto;margin:0;padding:12px}.top{padding:13px}.company h1{font-size:15px}.company p{font-size:6px;letter-spacing:2px}.logo{width:53px;height:34px}.meta{font-size:7px}.title h2{font-size:25px}.card{grid-template-columns:105px minmax(0,1fr);min-height:125px}.photo{min-height:125px}.info{padding:10px}.name{font-size:15px}.pricebox{grid-column:2;border-left:0;border-top:1px solid #7e632c;margin:0 10px 10px;padding:9px 0 0}.price{font-size:16px}.grid{gap:10px}.actions{display:flex;gap:7px;justify-content:center;flex-wrap:wrap}}

@media(max-width:480px){
 .sheet{padding:9px!important}
 .top{gap:9px;padding:10px!important;align-items:center}
 .company{gap:8px}
 .company h1{font-size:12px!important;line-height:1.25;letter-spacing:.2px}
 .company p{font-size:5px!important;letter-spacing:1.3px!important;line-height:1.5}
 .logo{width:42px!important;height:29px!important}
 .meta{font-size:6px!important;line-height:1.6}
 .title{padding:17px 0 12px}
 .title h2{font-size:23px!important;line-height:1.15}
 .eyebrow{font-size:7px;letter-spacing:2px}
 .card{grid-template-columns:92px minmax(0,1fr)!important;min-height:112px!important}
 .photo{grid-row:span 2;min-height:112px!important;padding:4px}
 .info{padding:9px!important}
 .cat{font-size:6px;letter-spacing:1.5px}
 .name{font-size:13px!important;line-height:1.25}
 .pricebox{grid-column:2!important;margin:0 8px 8px!important;padding:7px 0 0!important}
 .price-label{font-size:7px;letter-spacing:1.8px;margin-bottom:4px}
 .price{font-size:15px!important;white-space:normal;overflow-wrap:anywhere}
 .zoom-tools{left:3px;top:3px;gap:2px;padding:2px}
 .zoom-tools button{width:27px;height:27px;min-width:27px;min-height:27px;font-size:16px}
 .zoom-tools output{font-size:8px;min-width:24px}
 .foot{font-size:6px;letter-spacing:.5px;flex-wrap:wrap;line-height:1.6}
 .actions button{min-height:44px}
}

</style></head><body>
<div class="actions no-print"><button onclick="window.print()">🖨 CETAK / SIMPAN SEBAGAI PDF</button><a href="katalog.php">Kembali ke Katalog</a></div>
<div class="sheet"><header class="top"><div class="company"><img class="logo" src="assets/images/logo-afa.png" alt="AFA"><div><h1>AFA CIPTA KARYA NUSANTARA</h1><p>OFFICIAL PRODUCT CATALOGUE</p></div></div><div class="meta">OFFICIAL PRODUCT CATALOGUE<br><?=date('d M Y')?></div></header>
<section class="title"><div class="eyebrow">LUXURY SALES CATALOGUE</div><h2><?=e2($category?:'Semua Produk')?></h2><div class="chips"><span class="chip">Kategori: <?=e2($category?:'Semua')?></span><span class="chip"><?=count($products)?> Produk</span></div></section>
<section class="grid"><?php foreach($products as $p):?><article class="card"><div class="photo"><?php $imageUrl=product_image_url($p['image']); if($imageUrl):?><img src="<?=e2($imageUrl)?>" alt="<?=e2($p['name'])?>" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><div class="noimg" hidden>Foto belum tersedia</div><?php else:?><div class="noimg">Foto belum tersedia</div><?php endif;?></div><div class="info"><div class="cat"><?=e2($p['category']?:'Produk')?></div><div class="name"><?=e2($p['name'])?></div></div></article><?php endforeach;?></section>
<?php if(!$products):?><div style="padding:60px 10px;text-align:center;color:#888;font:italic 18px Georgia">Tidak ada produk untuk filter ini.</div><?php endif;?>
<footer class="foot"><span>AFA CIPTA KARYA NUSANTARA</span><span>OFFICIAL PRODUCT CATALOGUE</span></footer></div>
<script>
document.querySelectorAll('.photo').forEach(function(photo){
 const img=photo.querySelector('img'); if(!img)return;
 let z=1; const tools=document.createElement('div'); tools.className='zoom-tools no-print';
 tools.innerHTML='<button type="button" aria-label="Perkecil gambar">−</button><output>100%</output><button type="button" aria-label="Perbesar gambar">+</button><button type="button" aria-label="Reset zoom">↺</button>';
 const out=tools.querySelector('output'); function apply(){img.style.setProperty('--afa-zoom',z);out.textContent=Math.round(z*100)+'%';}
 tools.children[0].addEventListener('click',()=>{z=Math.max(.5,Math.round((z-.1)*10)/10);apply()});
 tools.children[2].addEventListener('click',()=>{z=Math.min(3,Math.round((z+.1)*10)/10);apply()});
 tools.children[3].addEventListener('click',()=>{z=1;apply()});
 let pts=new Map(),pinchDistance=0,pinchScale=1;
 photo.style.touchAction='none';
 photo.addEventListener('pointerdown',e=>{if(e.target.closest('.zoom-tools'))return;pts.set(e.pointerId,{x:e.clientX,y:e.clientY});if(pts.size===2){const a=[...pts.values()];pinchDistance=Math.hypot(a[0].x-a[1].x,a[0].y-a[1].y);pinchScale=z;}},{passive:true});
 photo.addEventListener('pointermove',e=>{if(!pts.has(e.pointerId))return;pts.set(e.pointerId,{x:e.clientX,y:e.clientY});if(pts.size===2&&pinchDistance){const a=[...pts.values()];const d=Math.hypot(a[0].x-a[1].x,a[0].y-a[1].y);z=Math.min(12,Math.max(1,pinchScale*d/pinchDistance));apply();}},{passive:true});
 ['pointerup','pointercancel','lostpointercapture'].forEach(k=>photo.addEventListener(k,e=>{pts.delete(e.pointerId);if(pts.size<2)pinchDistance=0;}));
 photo.appendChild(tools);
});
</script></body></html>