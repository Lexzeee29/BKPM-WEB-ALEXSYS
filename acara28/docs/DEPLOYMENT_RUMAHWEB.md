# Deployment ke Rumahweb

1. Buat database MySQL dan user melalui panel Rumahweb, lalu berikan user akses ke database tersebut.
2. Atur document root domain ke folder `public/`. Folder `app/`, `config/`, `database/`, dan `storage/` harus berada di luar document root.
3. Import `database/schema.sql` ke database Rumahweb yang sudah dipilih di phpMyAdmin. File ini tidak membuat database baru.
4. Buka `config/database.php` dan isi `dbname`, `username`, serta `password` dengan nilai lengkap dari menu **MySQL Databases** cPanel. Host biasanya `localhost`, tetapi ikuti informasi dari Rumahweb. Isi juga `jwt_secret` dengan secret acak minimal 32 karakter, dibuat sekali melalui terminal:
   ```sh
   php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'
   ```
5. Pastikan PHP dapat menulis ke `storage/logs/` dan `public/uploads/`.

Jangan publikasikan atau commit kredensial database dan `jwt_secret` ke repository.
