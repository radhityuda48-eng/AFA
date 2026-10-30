PATCH UPLOAD FOTO AFA KATALOG — VERSI AUDIT 2

PENTING: patch ini hanya mengganti file pengelolaan upload dan pengaturan upload opsional. Tidak ada database SQL di dalam patch dan jangan import SQL lagi.

CARA PASANG AMAN:
1. Di cPanel > File Manager, buka folder document root domain afaciptakaryanusantara.site (biasanya public_html).
2. Backup/download dulu file `admin/index.php` dan `assets/js/admin.js` yang sekarang.
3. Upload isi ZIP ini dengan mempertahankan struktur folder: `admin/index.php`, `assets/js/admin.js`, dan `.user.ini` harus berada di root document root sesuai jalur tersebut.
4. Jangan menghapus folder `uploads`, database, atau file produk lainnya.
5. Buka halaman admin lalu tekan Ctrl+F5. Versi JavaScript kini memakai cache-busting unik.
6. Pilih foto dan tunggu status menyatakan foto sudah dikompres/siap sebelum menekan Simpan Produk.
7. Jika kode 1 masih muncul, baca nilai `upload_max_filesize` dan `post_max_size` yang tampil. Hosting dapat mengabaikan `.user.ini`; jika demikian minta hosting mengatur upload_max_filesize minimal 2M dan post_max_size minimal 4M.

Catatan: `.user.ini` mencoba menetapkan upload_max_filesize=8M dan post_max_size=12M, tetapi hosting mungkin membatasi pengaturan tersebut. Perubahan dapat perlu beberapa menit untuk berlaku.
