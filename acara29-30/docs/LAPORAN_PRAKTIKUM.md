# Laporan Akhir Praktikum SI Akademik

## Ringkasan Proyek

Proyek ini membangun aplikasi Sistem Informasi Akademik berbasis PHP dengan pola MVC, API JSON, autentikasi sesi untuk halaman web, dan token Bearer untuk operasi tulis API mahasiswa. Tahap acara 17 menjadi fondasi front controller dan CRUD; pengerjaan berikutnya melengkapi model, controller, autentikasi, unggah foto, API, pengujian, backup, dan persiapan deployment acara 30.

## Arsitektur Akhir

```text
Browser
  |-- Halaman web / form
  |     `-- public/index.php (Front Controller + routes/web.php)
  |           |-- app/Core (Router, Auth, Middleware, Response)
  |           |-- app/Controllers (AuthController, MahasiswaController, ...)
  |           |-- app/Services + app/Repositories
  |           |-- app/Models
  |           `-- public/views (PHP Views, app.js, form_edit.js)
  |
  `-- REST API
        `-- public/index.php -> api/mahasiswa.php / app/Controllers
              `-- app/Models -> MySQL

public/uploads (foto)                 storage/logs (log privat)
```

### Tanggung jawab komponen

- `public/index.php`: menerima request, menentukan base path, dan meneruskan request ke route web/API.
- `app/Core/`: komponen bersama untuk routing, autentikasi, middleware, respons, autoload, dan logging.
- `app/Controllers/`: mengatur alur autentikasi serta operasi aplikasi mahasiswa dan sumber daya lain.
- `app/Models/`, `app/Repositories/`, `app/Services/`: akses data dan logika domain.
- `public/views/`: tampilan autentikasi dan dashboard. Data API dirender menggunakan DOM dan `textContent` sehingga data dinamis tidak diinterpretasikan sebagai HTML.
- `public/uploads/`: berkas foto mahasiswa yang telah divalidasi.

## Alur Autentikasi dan Data

1. Pengguna masuk melalui `/login`; `AuthController` memverifikasi akun lalu membuat sesi web.
2. Halaman dashboard memanggil API mahasiswa. Admin memperoleh token Bearer untuk operasi tulis, sedangkan pembacaan data mengikuti kebijakan endpoint.
3. Tambah dan ubah data mengirim form-data; foto opsional dibatasi ke JPG/PNG maksimal 2MB. Hapus meminta konfirmasi.
4. Request view otomatis dicoba ulang satu kali hanya saat `fetch` gagal pada tingkat jaringan. Respons HTTP gagal tetap ditampilkan sebagai kegagalan dan tidak diulang.
5. Form menonaktifkan tombol selama pengiriman dan menyampaikan status berhasil/gagal. Seluruh output dinamis pada view menggunakan escaping HTML server-side atau API DOM aman (`textContent`).

## Persiapan Produksi dan Checklist Serah Terima

Base URL deployment: **https://alwiyaa.my.id/Alex/public**.

| Pemeriksaan | Status |
| --- | --- |
| AutoSSL aktif dan halaman dapat diakses melalui HTTPS | Perlu diverifikasi di cPanel |
| HTTP dialihkan ke HTTPS oleh `public/.htaccess` | Aturan disiapkan; verifikasi setelah upload |
| `display_errors` nonaktif dan error logging aktif | Perlu dikonfigurasi pada MultiPHP INI Editor |
| Endpoint API dan autentikasi diuji pada hosting | Belum dijalankan dari lingkungan ini |
| Alur login → dashboard → tambah foto → edit → hapus diuji | Belum dijalankan pada hosting |
| Nama berisi markup HTML tampil sebagai teks biasa | Render memakai `textContent`; tetap verifikasi manual |
| Dokumentasi API menggunakan base URL produksi | Selesai |
| Kredensial database dan JWT secret tidak dipublikasikan | Periksa konfigurasi sebelum serah-terima |

Jalankan kembali test case pada `TEST_CASE.md` di hosting dengan data uji yang aman. Jangan memasukkan kredensial atau token produksi ke Postman collection maupun laporan.

## Simulasi Pemulihan Bencana

Simulasi penghapusan tabel tidak dijalankan pada database ini agar data yang ada tidak dirusak. Untuk latihan pada database percobaan saja:

1. Buat ekspor SQL lengkap dan pastikan file backup dapat dibaca.
2. Siapkan database percobaan terpisah; jangan gunakan database produksi.
3. Hapus satu tabel uji yang dipilih, lalu impor backup yang sesuai melalui phpMyAdmin atau MySQL.
4. Verifikasi tabel, jumlah data, dan relasi yang dipulihkan sebelum mencatat waktu dan hasil simulasi.

Simpan backup dengan pola `si_akademik_YYYY-MM-DD_HH-mm.sql` di lokasi privat, bukan di bawah document root.

## Catatan Foto Mahasiswa

`MahasiswaController::update()` menghapus foto lama setelah pembaruan berhasil dan hanya jika nama foto berubah. Pada pembaruan gagal, foto baru yang sempat diunggah dibersihkan melalui helper `removePhoto()`.
