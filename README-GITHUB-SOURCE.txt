PAKET SOURCE AFA UNTUK GITHUB — VERSI RINGAN
============================================

Mengapa folder uploads tidak disertakan?
- Folder uploads berisi foto produk asli dengan ukuran besar (sekitar 33 MB).
- Foto produk adalah konten/data website, bukan source code aplikasi.
- Jika website AFA saat ini sudah berjalan, foto tetap berada di hosting dan tidak perlu diunggah ulang ke GitHub.
- Folder uploads tetap dibuat dengan .gitkeep agar struktur folder tersedia.

CARA MENGGUNAKAN
1. Unduh dan ekstrak ZIP ini di komputer.
2. Buat repository GitHub PRIVATE.
3. Upload isi folder AFA_Offline_App_v1 ke repository (bukan ZIP-nya).
4. Untuk upload melalui GitHub web, gunakan Add file > Upload files. Jika jumlah file banyak, gunakan GitHub Desktop atau git push.
5. Pastikan file config.local.php, password, token, dan kredensial tidak dimasukkan ke repository.
6. Workflow build tersedia pada .github/workflows.
7. Untuk deployment website, jangan menimpa folder uploads yang sudah ada di hosting.

PENTING
- Paket ini tidak berisi foto produk, database produksi, kredensial, atau file rahasia.
- Untuk pengembangan lokal baru, salin foto produk dari backup hosting ke folder uploads secara terpisah.
- Foto di server produksi tidak akan terhapus jika Anda hanya mengunggah source code ke GitHub.
- Gunakan repository PRIVATE karena source code website adalah aset internal.
