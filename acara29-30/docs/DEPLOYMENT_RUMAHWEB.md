# Deployment ke Rumahweb

Base URL produksi aplikasi: `https://alwiyaa.my.id/Alex/public`.

1. Buat database MySQL dan user melalui panel Rumahweb, lalu berikan user akses ke database tersebut.
2. Pastikan document root mengarah ke folder `public/`. Jika struktur hosting mengharuskan folder aplikasi berada di bawah document root, pertahankan `app/.htaccess` sebagai lapisan penolak akses tambahan.
3. Import `database/schema.sql` ke database Rumahweb yang sudah dipilih di phpMyAdmin. File ini tidak membuat database baru.
4. Buka `config/database.php` dan isi `dbname`, `username`, serta `password` dengan nilai lengkap dari menu **MySQL Databases** cPanel. Host biasanya `localhost`, tetapi ikuti informasi dari Rumahweb. Isi juga `jwt_secret` dengan secret acak minimal 32 karakter, dibuat sekali melalui terminal:
   ```sh
   php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'
   ```
5. Pastikan PHP dapat menulis ke `storage/logs/` dan `public/uploads/`.
6. Di cPanel **SSL/TLS Status**, jalankan AutoSSL untuk `alwiyaa.my.id` dan verifikasi situs dapat dibuka melalui HTTPS. Aturan pada `public/.htaccess` mengalihkan seluruh HTTP ke HTTPS sebelum front controller.
7. Pada **MultiPHP INI Editor**, atur `display_errors = Off`, `log_errors = On`, dan arahkan `error_log` ke file log privat di luar document root (contoh: `/home/namacpanel/logs/php_errors.log`). Pastikan folder log tersedia dan dapat ditulis oleh PHP.
8. Uji login, pemuatan dashboard, operasi tambah/ubah/hapus mahasiswa, unggah foto, serta input nama berisi teks seperti `<script>alert(1)</script>`. Pastikan teks tampil sebagai teks biasa dan tidak dieksekusi. Lihat checklist serah-terima pada `LAPORAN_PRAKTIKUM.md`.

Jangan publikasikan atau commit kredensial database dan `jwt_secret` ke repository.
