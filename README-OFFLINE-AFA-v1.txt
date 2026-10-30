AFA OFFLINE APP — VERSI 1 (PWA + ADMIN OFFLINE + SINKRONISASI)

ISI DAN BATASAN VERSI 1
- PWA dapat dipasang di Android, iPhone/iPad (Add to Home Screen), Windows, dan macOS melalui browser yang mendukung instalasi PWA. Paket ini belum menghasilkan APK native bertanda tangan, EXE, atau DMG.
- Halaman publik, CSS/JS, API katalog, dan gambar yang pernah dibuka saat online disimpan cache untuk penggunaan offline.
- Admin offline menyediakan antrean tambah/hapus produk lokal. Foto boleh disimpan sebagai Blob di perangkat.
- Saat online, antrean dikirim ke API PHP dan perubahan diambil kembali dari MySQL.
- Tidak ada modul inventaris atau penjualan pada versi ini.
- Mode admin offline perlu diaktifkan dengan membuka admin online sekali dan membuat PIN lokal di perangkat itu.

PENTING SEBELUM DIPASANG
1. Backup penuh website dan database produksi.
2. ZIP ini sengaja TIDAK menyertakan kredensial database atau hash password lama.
3. Salin config.local.php.example menjadi config.local.php dan isi host/database/user/password.
4. Buat hash password admin/sales baru dengan PHP password_hash(), lalu isi config.local.php.
5. Pastikan config.local.php dilindungi dari akses publik (.htaccess disertakan).
6. Jalankan database/migration_offline_v1.sql di database yang sama setelah backup. Migrasi hanya membuat tabel afa_sync_mutations; tidak menghapus tabel products.
7. Upload seluruh isi paket ke document root (umumnya public_html) dengan struktur folder dipertahankan.
8. Pastikan website berjalan di HTTPS. Service Worker dan IndexedDB memerlukan secure context (localhost untuk pengembangan).
9. Setelah upload, buka website online, refresh keras, buka katalog dan biarkan gambar produk dimuat agar cache terisi.
10. Login ke /admin/index.php saat online. Buat PIN lokal pada panel Aktivasi Admin Offline. Kunjungi /admin/offline.html sekali agar shell tersimpan.
11. Uji di perangkat percobaan terlebih dahulu: tambah produk offline, sinkronkan, cek produk di perangkat lain.

INSTALASI PER PLATFORM
- Android: buka domain di Chrome/Edge, pilih Install app / Tambahkan ke layar utama.
- iPhone/iPad: buka domain di Safari > Share > Add to Home Screen. iOS membatasi beberapa fitur background; sinkronisasi terjadi saat aplikasi dibuka dan online.
- Windows: buka domain di Edge/Chrome, gunakan ikon Install di address bar/menu. Untuk installer .EXE native diperlukan tahap pembungkus desktop terpisah.
- macOS: Safari/Chrome dapat menambahkan PWA ke Dock atau layar aplikasi sesuai versi OS/browser. Untuk paket .DMG native diperlukan tahap pembungkus desktop terpisah.

KEAMANAN DAN KETERBATASAN
- Halaman admin server tidak dimasukkan ke cache Service Worker. Admin offline memakai shell lokal dan PIN perangkat.
- Sesi admin server harus masih valid ketika melakukan sinkronisasi; bila kedaluwarsa, login online kembali.
- Perangkat harus pernah online dan mengunduh katalog agar data produk tersedia offline.
- Cache browser dapat dihapus oleh pengguna/OS; jangan jadikan cache sebagai satu-satunya arsip data penting.
- Produk yang ditambahkan offline baru memperoleh ID server setelah sinkronisasi.
- V1 mendukung tambah dan hapus dari mode offline. Pengeditan produk offline belum disediakan karena UI lama hanya memiliki fitur tambah/hapus.
- Sinkronisasi otomatis dipicu ketika koneksi kembali atau halaman admin offline terbuka; browser/OS tidak menjamin background sync saat aplikasi benar-benar ditutup.
- Produk dengan perubahan bersamaan pada beberapa perangkat perlu aturan resolusi konflik. V1 menggunakan operasi tambah/hapus idempoten dan tidak menerapkan edit produk offline.

ROLLBACK
- Pulihkan file backup yang dibuat sebelum pemasangan.
- Tabel afa_sync_mutations dapat dibiarkan tanpa memengaruhi tabel products; jangan drop tabel tanpa backup.

UJI TEKNIS YANG DILAKUKAN PADA PAKET
- Sintaks PHP diperiksa dengan php -l jika PHP CLI tersedia.
- JavaScript diperiksa secara sintaksis jika Node tersedia.
- Ini bukan pengujian langsung terhadap akun hosting/produksi; lakukan uji staging sebelum deploy.
