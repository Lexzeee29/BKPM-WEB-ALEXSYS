# Laporan Pengujian Manual dan Blackbox

## Informasi Pengujian

- **Aplikasi:** Sistem Informasi Akademik — API autentikasi dan mahasiswa
- **Tanggal:** Isi tanggal pelaksanaan pengujian
- **Metode:** Manual blackbox testing dengan equivalence partitioning
- **Lingkungan:** Laragon, PHP, MySQL, API lokal
- **Base URL:** `http://localhost/BKPM-WEB-ALEXX/acara23/public` (sesuaikan dengan konfigurasi Laragon)
- **Hasil:** Belum dapat dihitung; skenario belum dieksekusi pada server lokal

Pengujian dilakukan dengan mengirim request HTTP dan memeriksa status serta body response. Prosedur berikut menjelaskan reproduksi melalui Postman. Kolom hasil aktual dan status harus diisi berdasarkan response yang benar-benar diterima; hasil tidak diasumsikan lulus sebelum dieksekusi.

Sebelum login, konfigurasi environment server dengan `JWT_SECRET` minimal 32 karakter. Jangan menyimpan secret atau password produksi di Postman collection maupun laporan.

## Ruang Lingkup dan Pemetaan Route

| Method | Endpoint aktual | Controller / handler | Autentikasi |
| --- | --- | --- | --- |
| POST | `/api/register` | `AuthController::register` | Tidak |
| POST | `/api/login` | `AuthController::loginApi` | Tidak |
| GET | `/api/mahasiswa` | `api/mahasiswa.php` | Tidak |
| GET | `/api/mahasiswa/{id}` | `api/mahasiswa.php` | Tidak |
| POST | `/api/mahasiswa` | `api/mahasiswa.php` | Bearer JWT |
| PUT | `/api/mahasiswa/{id}` | `api/mahasiswa.php` | Bearer JWT |
| DELETE | `/api/mahasiswa/{id}` | `api/mahasiswa.php` | Bearer JWT |

Rute API mahasiswa diteruskan ke `api/mahasiswa.php`. Rute web `/mahasiswa` adalah halaman berbasis sesi; update web menggunakan POST `/mahasiswa/{id}` dan delete web menggunakan POST `/mahasiswa/{id}/delete`. Pengujian REST di laporan ini mengikuti endpoint API, bukan route form web.

Body JSON untuk register, login, POST, dan PUT dikirim dengan `Content-Type: application/json`. Operasi tulis mahasiswa memerlukan `Authorization: Bearer <token>`. Field wajib mahasiswa adalah `nim`, `nama`, dan `email`; NIM harus 9 digit dan unik. `prodi_id` dan `angkatan` dapat dihilangkan saat POST karena API menyediakan nilai default. Saat PUT, nilai `prodi_id` dan `angkatan` yang tidak dikirim akan dipertahankan.

## Data Uji

- `{{base_url}}`: URL instalasi lokal; contoh di atas.
- `{{username_uji}}`, `{{email_uji}}`, `{{password_uji}}`: akun baru yang belum terdaftar.
- `{{token}}`: token dari response login berhasil; token berlaku 5 menit.
- `{{id_uji}}`: ID mahasiswa sementara yang didapat dari TC05.
- `{{nim_uji}}`: NIM 9 digit yang belum terdaftar.
- `{{nim_terpakai}}`: NIM 9 digit milik record lain. Data seed proyek memuat NIM `241250598`.

Contoh body mahasiswa valid:

```json
{
  "nim": "{{nim_uji}}",
  "nama": "Mahasiswa Uji Acara 23",
  "email": "uji-acara23@example.com",
  "prodi_id": 1,
  "angkatan": 2026
}
```

## Test Case dan Hasil

| ID | Skenario pengujian | Data uji | Hasil yang diharapkan | Hasil aktual | Status |
| --- | --- | --- | --- | --- | --- |
| TC00 | Mengambil daftar mahasiswa | `GET /api/mahasiswa` | `200 OK`, daftar data dikembalikan | Belum dieksekusi | Belum diuji |
| TC01 | Registrasi akun uji | `POST /api/register`, username/email unik dan password terisi | `201 Created`, ID akun dikembalikan | Belum dieksekusi | Belum diuji |
| TC02 | Login dengan kredensial benar | `POST /api/login`, username dan password dari TC01 | `200 OK`, token tersedia di `data.token` | Belum dieksekusi | Belum diuji |
| TC03 | Login dengan password salah | `POST /api/login`, username TC01 dan password salah | `401 Unauthorized` | Belum dieksekusi | Belum diuji |
| TC04 | Store tanpa token | `POST /api/mahasiswa`, body valid tanpa Authorization | `401 Unauthorized` | Belum dieksekusi | Belum diuji |
| TC05 | Store dengan data valid | `POST /api/mahasiswa`, body valid dan token; simpan ID response sebagai `id_uji` | `201 Created`, data mahasiswa dikembalikan | Belum dieksekusi | Belum diuji |
| TC06 | Store dengan NIM kosong | `POST /api/mahasiswa`, `nim: ""`, field lain valid dan token | `400 Bad Request`, validasi field wajib dikembalikan | Belum dieksekusi | Belum diuji |
| TC07 | Store dengan format NIM tidak valid | `POST /api/mahasiswa`, `nim: "12A45"`, field lain valid dan token | `422 Unprocessable Content`, format NIM ditolak | Belum dieksekusi | Belum diuji |
| TC08 | Store dengan NIM duplikat | `POST /api/mahasiswa`, `nim: "{{nim_terpakai}}"`, field lain valid dan token | `409 Conflict`, pesan NIM sudah terdaftar | Belum dieksekusi | Belum diuji |
| TC09 | Update dengan data valid | `PUT /api/mahasiswa/{{id_uji}}`, NIM tetap dan seluruh field valid; token | `200 OK`, data diperbarui | Belum dieksekusi | Belum diuji |
| TC10 | Update dengan NIM kosong | `PUT /api/mahasiswa/{{id_uji}}`, `nim: ""`, field lain valid dan token | `400 Bad Request`, validasi NIM wajib diisi | Belum dieksekusi | Belum diuji |
| TC11 | Update dengan NIM duplikat | `PUT /api/mahasiswa/{{id_uji}}`, gunakan NIM record lain dan token | `409 Conflict`, NIM sudah terdaftar; data target tidak berubah | Belum dieksekusi | Belum diuji |
| TC12 | Update dengan email tidak valid | `PUT /api/mahasiswa/{{id_uji}}`, `email: "bukan-email"`, field lain valid dan token | `400 Bad Request`, format email ditolak | Belum dieksekusi | Belum diuji |
| TC13 | Update dengan ID tidak ditemukan | `PUT /api/mahasiswa/999999999`, body valid dan token | `404 Not Found` | Belum dieksekusi | Belum diuji |
| TC14 | Delete tanpa token | `DELETE /api/mahasiswa/{{id_uji}}`, tanpa Authorization | `401 Unauthorized` | Belum dieksekusi | Belum diuji |
| TC15 | Delete data yang ada | `DELETE /api/mahasiswa/{{id_uji}}`, token valid | `200 OK`, data yang dihapus dikembalikan | Belum dieksekusi | Belum diuji |
| TC16 | Delete ID tidak ditemukan | `DELETE /api/mahasiswa/999999999`, token valid | `404 Not Found` | Belum dieksekusi | Belum diuji |

TC09–TC16 menyediakan delapan kasus untuk update dan destroy, termasuk data valid, data tidak valid, NIM duplikat, ID tidak ditemukan, dan autentikasi.

## Langkah Reproduksi di Postman

1. Pastikan Apache dan MySQL Laragon aktif, database `si_akademik` tersedia, dan `base_url` mengarah ke folder `acara23/public`.
2. Impor `api/postman_collection.json` dan `api/SI-Akademik-Local.postman_environment.json`. Sesuaikan URL, lalu isi username, email, password, NIM, dan ID uji. Jangan membagikan environment yang berisi kredensial atau token.
3. Jalankan TC01 untuk membuat akun, lalu TC02 untuk login. Simpan `data.token` sebagai variable `token`. Jalankan TC03 dengan password berbeda.
4. Jalankan TC00 untuk memeriksa data dan menentukan NIM yang telah terpakai. Jalankan TC04, lalu TC05 untuk membuat mahasiswa uji; simpan `data.id` sebagai `id_uji`.
5. Jalankan TC06–TC08 untuk menguji variasi store. Pastikan `nim_terpakai` merupakan NIM 9 digit yang valid dan sudah tersimpan.
6. Jalankan TC09–TC13 untuk menguji variasi update. Setelah kasus gagal, gunakan GET `/api/mahasiswa/{{id_uji}}` untuk memastikan data target tidak berubah.
7. Jalankan TC14 sebelum TC15, karena TC15 menghapus data uji. Jalankan TC16 setelahnya untuk memeriksa ID yang sudah tidak ada.
8. Catat status HTTP dan ringkasan body yang diterima pada kolom hasil aktual. Tandai Pass hanya jika status dan perilaku sesuai hasil yang diharapkan; jika tidak, tandai Fail dan simpan bukti response.
9. Jika ditemukan Fail, telusuri handler/API, Model, atau Controller yang mengendalikan perilaku tersebut, lakukan perbaikan, lalu ulangi kasus terkait dan catat hasil retest.

## Kesimpulan

Laporan ini memetakan 17 skenario terhadap kontrak API yang tersedia pada proyek. Pengujian manual belum dijalankan pada server lokal dalam penyusunan dokumen ini, sehingga hasil aktual belum dapat dinyatakan lulus atau gagal. Isi hasil aktual dan status setelah menjalankan seluruh request di Postman.