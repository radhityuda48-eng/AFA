PANDUAN DEPLOYMENT AFA

1. Upload seluruh file ke document root hosting dengan struktur folder dipertahankan.
2. Salin config.local.php.example menjadi config.local.php dan isi kredensial database.
3. Isi hash password baru pada config.local.php menggunakan password_hash() PHP.
4. Pastikan HTTPS aktif.
5. Jangan membagikan config.local.php atau file backup yang berisi kredensial.
6. Untuk fitur offline, ikuti README-OFFLINE-AFA-v1.txt.
