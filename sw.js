/* AFA PWA service worker - public pages and assets only. Admin server pages are never cached. */
const CACHE='afa-shell-v1';
const PUBLIC_CACHE='afa-public-v1';
const ASSET_CACHE='afa-assets-v1';
const CORE=['/offline.html','/admin/offline.html','/manifest.webmanifest','/assets/css/style.css','/assets/js/katalog.js','/assets/js/image-zoom.js','/assets/js/produk.js','/assets/js/offline-admin-shell.js','/assets/images/logo-afa.png','/assets/images/icon-192.png','/assets/images/icon-512.png','/assets/images/apple-touch-icon.png','/assets/images/favicon.ico'];
self.addEventListener('install',event=>event.waitUntil((async()=>{const c=await caches.open(CACHE);await c.addAll(CORE);await self.skipWaiting()})()));
self.addEventListener('activate',event=>event.waitUntil((async()=>{for(const k of await caches.keys())if(![CACHE,PUBLIC_CACHE,ASSET_CACHE].includes(k))await caches.delete(k);await self.clients.claim()})()));
function isSensitive(url){return /\/(admin\/index\.php|login\.php|logout\.php|print\.php|customer-print\.php|api\/sync\/)/i.test(url.pathname)||url.pathname.includes('config.local.php')}
self.addEventListener('fetch',event=>{
 const req=event.request;if(req.method!=='GET')return;
 const url=new URL(req.url);if(url.origin!==self.location.origin||isSensitive(url))return;
 if(url.pathname.startsWith('/uploads/')){event.respondWith((async()=>{const c=await caches.open(ASSET_CACHE);const hit=await c.match(req);if(hit)return hit;try{const r=await fetch(req);if(r.ok)c.put(req,r.clone());return r}catch(e){return hit||Response.error()}})());return}
 if(url.pathname==='/api/products.php'){event.respondWith((async()=>{const c=await caches.open(PUBLIC_CACHE);try{const r=await fetch(req);if(r.ok)c.put(req,r.clone());return r}catch(e){const exact=await c.match(req);if(exact)return exact;const base=await c.match('/api/products.php');if(base){let rows=await base.json();const q=(url.searchParams.get('q')||'').toLowerCase(),cat=url.searchParams.get('category')||'',brand=url.searchParams.get('brand')||'';rows=rows.filter(p=>(!q||[p.name,p.sku,p.brand].some(v=>String(v||'').toLowerCase().includes(q)))&&(!cat||p.category===cat)&&(!brand||p.brand===brand));return new Response(JSON.stringify(rows),{headers:{'Content-Type':'application/json; charset=utf-8'}})}return new Response('[]',{headers:{'Content-Type':'application/json; charset=utf-8'}})}})());return}
 if(req.mode==='navigate' && /\.(php|html)?$/.test(url.pathname)){
  event.respondWith((async()=>{const c=await caches.open(PUBLIC_CACHE);try{const r=await fetch(req);if(r.ok)c.put(req,r.clone());return r}catch(e){return await c.match(req)||await caches.match('/offline.html')||new Response('AFA sedang offline. Buka kembali halaman yang pernah dikunjungi.',{status:503,headers:{'Content-Type':'text/html; charset=utf-8'}})}})());return
 }
 if(['style','script','image','font'].includes(req.destination)){event.respondWith((async()=>{const c=await caches.open(ASSET_CACHE);const hit=await c.match(req);if(hit)return hit;try{const r=await fetch(req);if(r.ok)c.put(req,r.clone());return r}catch(e){return hit||Response.error()}})())}
});
self.addEventListener('message',event=>{if(event.data==='AFA_SKIP_WAITING')self.skipWaiting()});
