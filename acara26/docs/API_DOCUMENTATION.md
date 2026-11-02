# Dokumentasi API SI Akademik

Dokumen ini menjelaskan endpoint JSON yang tersedia pada aplikasi SI Akademik, termasuk autentikasi, mahasiswa, dan buku. Endpoint JSON mahasiswa ditangani oleh `api/mahasiswa.php`; halaman web mahasiswa menggunakan `MahasiswaController` dan autentikasi sesi, bukan Bearer token.

## Ringkasan Endpoint API

Base URL lokal mengikuti lokasi instalasi aplikasi. Contoh jika aplikasi diakses melalui XAMPP:

```text
http://localhost/BKPM-WEB-ALEXX/acara21/public
```

Tambahkan path endpoint di bawah ke Base URL tersebut.

| Method | Endpoint | Controller/handler | Autentikasi | Keterangan |
| --- | --- | --- | --- | --- |
| POST | `/api/register` | `AuthController::register()` | Tidak | Membuat akun pengguna |
| POST | `/api/login` | `AuthController::loginApi()` | Tidak | Login dan memperoleh token |
| GET | `/api/mahasiswa` | `api/mahasiswa.php` | Tidak | Mengambil seluruh data mahasiswa |
| GET | `/api/mahasiswa/{id}` | `api/mahasiswa.php` | Tidak | Mengambil detail mahasiswa |
| POST | `/api/mahasiswa` | `api/mahasiswa.php` | Bearer token | Menambahkan mahasiswa |
| PUT | `/api/mahasiswa/{id}` | `api/mahasiswa.php` | Bearer token | Memperbarui mahasiswa |
| DELETE | `/api/mahasiswa/{id}` | `api/mahasiswa.php` | Bearer token | Menghapus mahasiswa |
| GET | `/api/buku` | `BukuController::index()` | Tidak | Mengambil seluruh buku |
| GET | `/api/buku/{id}` | `BukuController::show()` | Tidak | Mengambil detail buku |
| POST | `/api/buku` | `BukuController::store()` | Tidak | Menambahkan buku |
| PUT | `/api/buku/{id}` | `BukuController::update()` | Tidak | Memperbarui buku |
| DELETE | `/api/buku/{id}` | `BukuController::destroy()` | Tidak | Menghapus buku |

`{id}` adalah bilangan bulat positif. Semua body JSON harus dikirim dengan header `Content-Type: application/json`. Endpoint yang memerlukan autentikasi menerima header:

```text
Authorization: Bearer <token>
```

Token diperoleh dari `/api/login` dan berlaku selama 300 detik (5 menit). Endpoint API mahasiswa untuk membaca data bersifat publik; operasi tambah, ubah, dan hapus memerlukan token. Endpoint buku saat ini tidak menerapkan autentikasi.

## Autentikasi

### POST `/api/register`

Mendaftarkan akun baru. Kirim body JSON dengan field berikut:

| Field | Tipe | Wajib | Keterangan |
| --- | --- | --- | --- |
| `username` | string | Ya | Nama pengguna unik |
| `email` | string | Ya | Alamat email valid dan unik |
| `password` | string | Ya | Kata sandi |

Contoh request:

```json
{
  "username": "admin",
  "email": "admin@example.com",
  "password": "rahasia123"
}
```

Response `201 Created`:

```json
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": {
    "id": 1
  },
  "errors": [],
  "timestamp": "2026-10-26T12:00:00+00:00"
}
```

Kemungkinan response gagal:

| Status | Kondisi |
| --- | --- |
| `422 Unprocessable Content` | Field wajib kosong atau format email tidak valid |
| `409 Conflict` | Username atau email sudah terdaftar |
| `400 Bad Request` | Registrasi gagal karena kesalahan penyimpanan |

Contoh `422`:

```json
{
  "success": false,
  "message": "Username, email, dan password wajib diisi",
  "data": null,
  "errors": {
    "username": "Username wajib diisi"
  },
  "timestamp": "2026-10-26T12:00:00+00:00"
}
```

### POST `/api/login`

Memverifikasi kredensial dan menghasilkan token Bearer.

Request body:

```json
{
  "username": "admin",
  "password": "rahasia123"
}
```

Response `200 OK`:

```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "<jwt-token>",
    "role": "user"
  },
  "errors": [],
  "timestamp": "2026-10-26T12:00:00+00:00"
}
```

Gunakan nilai `data.token` pada header `Authorization: Bearer <token>` untuk operasi tulis API mahasiswa.

| Status | Kondisi |
| --- | --- |
| `400 Bad Request` | JSON tidak valid atau body bukan object JSON |
| `401 Unauthorized` | Username atau password salah |
| `415 Unsupported Media Type` | `Content-Type` bukan `application/json` |
| `422 Unprocessable Content` | Username atau password kosong |

## API Mahasiswa

### Model data

| Field | Tipe | Keterangan |
| --- | --- | --- |
| `id` | integer | ID data mahasiswa |
| `nim` | string | Wajib, tepat 9 digit dan unik |
| `nama` | string | Wajib, maksimal 100 karakter |
| `email` | string | Wajib, email valid dan maksimal 100 karakter |
| `prodi_id` | integer | ID program studi yang tersedia |
| `angkatan` | integer | Tahun antara 2000 dan 2155 |
| `status` | string | Status mahasiswa, misalnya `aktif` |

### GET `/api/mahasiswa`

Mengambil semua mahasiswa. Tidak memerlukan body atau token.

Response `200 OK`:

```json
{
  "success": true,
  "message": "Data mahasiswa berhasil diambil",
  "data": [
    {
      "id": 1,
      "nim": "241250598",
      "nama": "Rizki Pora",
      "email": "rizki@example.com",
      "prodi_id": 2,
      "angkatan": 2024,
      "status": "aktif"
    }
  ]
}
```

### GET `/api/mahasiswa/{id}`

Mengambil satu mahasiswa berdasarkan ID. Tidak memerlukan body atau token.

Response `200 OK`:

```json
{
  "success": true,
  "message": "Data mahasiswa berhasil diambil",
  "data": {
    "id": 1,
    "nim": "241250598",
    "nama": "Rizki Pora",
    "email": "rizki@example.com",
    "prodi_id": 2,
    "angkatan": 2024,
    "status": "aktif"
  }
}
```

Response gagal: `404 Not Found` jika ID tidak ditemukan.

### POST `/api/mahasiswa`

Menambahkan mahasiswa. Memerlukan Bearer token dan `Content-Type: application/json`.

Field wajib: `nim`, `nama`, dan `email`. Field `prodi_id` bersifat opsional dan secara default memakai program studi dengan ID terkecil. Field `angkatan` bersifat opsional dan secara default memakai tahun berjalan.

Request body:

```json
{
  "nim": "241250599",
  "nama": "Dewi Lestari",
  "email": "dewi@example.com",
  "prodi_id": 2,
  "angkatan": 2024
}
```

Response `201 Created`:

```json
{
  "success": true,
  "message": "Data mahasiswa berhasil ditambahkan",
  "data": {
    "id": 5,
    "nim": "241250599",
    "nama": "Dewi Lestari",
    "email": "dewi@example.com",
    "prodi_id": 2,
    "angkatan": 2024,
    "status": "aktif"
  }
}
```

### PUT `/api/mahasiswa/{id}`

Memperbarui mahasiswa yang sudah ada. Memerlukan Bearer token dan `Content-Type: application/json`.
 Kirim kembali field wajib `nim`, `nama`, dan `email`. Jika `prodi_id` atau `angkatan` tidak dikirim, nilai yang sudah tersimpan tetap digunakan.

Request body:

```json
{
  "nim": "241250599",
  "nama": "Dewi Lestari Putri",
  "email": "dewi.putri@example.com",
  "prodi_id": 2,
  "angkatan": 2024
}
```

Response `200 OK`:

```json
{
  "success": true,
  "message": "Data mahasiswa berhasil diubah",
  "data": {
    "id": 5,
    "nim": "241250599",
    "nama": "Dewi Lestari Putri",
    "email": "dewi.putri@example.com",
    "prodi_id": 2,
    "angkatan": 2024,
    "status": "aktif"
  }
}
```

### DELETE `/api/mahasiswa/{id}`

Menghapus mahasiswa. Memerlukan Bearer token. Tidak memerlukan body.

Response `200 OK`:

```json
{
  "success": true,
  "message": "Data mahasiswa berhasil dihapus",
  "data": {
    "id": 5,
    "nim": "241250599",
    "nama": "Dewi Lestari Putri",
    "email": "dewi.putri@example.com",
    "prodi_id": 2,
    "angkatan": 2024,
    "status": "aktif"
  }
}
```

### Response gagal API mahasiswa

| Status | Kondisi |
| --- | --- |
| `400 Bad Request` | JSON tidak valid, body bukan object, data wajib tidak lengkap, atau `prodi_id`/`angkatan` tidak valid |
| `401 Unauthorized` | Token tidak ditemukan, tidak valid, atau kedaluwarsa |
| `404 Not Found` | Endpoint atau data mahasiswa tidak ditemukan |
| `409 Conflict` | NIM sudah terdaftar |
| `415 Unsupported Media Type` | Body tulis bukan `application/json` |
| `422 Unprocessable Content` | Format NIM salah atau belum ada data program studi |
| `500 Internal Server Error` | Terjadi kesalahan saat memproses data mahasiswa |

Contoh response gagal validasi:

```json
{
  "success": false,
  "message": "Data request tidak lengkap atau tidak valid",
  "data": null
}
```

## API Buku

Endpoint buku tidak memerlukan autentikasi. Body untuk operasi tambah dan ubah harus memiliki `judul`, `penulis`, `penerbit`, dan `tahun_terbit`.

| Field | Tipe | Validasi |
| --- | --- | --- |
| `judul` | string | Wajib, maksimal 150 karakter |
| `penulis` | string | Wajib, maksimal 100 karakter |
| `penerbit` | string | Wajib, maksimal 100 karakter |
| `tahun_terbit` | integer | Wajib, antara 1000 dan 65535 |

### GET `/api/buku` dan GET `/api/buku/{id}`

Mengambil seluruh buku atau buku berdasarkan ID. Response sukses `200 OK`:

```json
{
  "success": true,
  "message": "Data buku berhasil diambil",
  "data": {
    "id": 1,
    "judul": "Belajar PHP",
    "penulis": "Andi Saputra",
    "penerbit": "Pustaka Web",
    "tahun_terbit": 2025,
    "created_at": "2026-10-26 12:00:00"
  }
}
```

Pada `GET /api/buku`, `data` berisi array buku. ID yang tidak ditemukan menghasilkan `404 Not Found`.

### POST `/api/buku`

Request body:

```json
{
  "judul": "Belajar PHP",
  "penulis": "Andi Saputra",
  "penerbit": "Pustaka Web",
  "tahun_terbit": 2025
}
```

Response `201 Created` dengan `data` berupa objek buku yang berhasil dibuat.

### PUT `/api/buku/{id}`

Kirim semua field buku yang diwajibkan seperti pada POST. Response `200 OK` dengan `data` berisi objek buku yang diperbarui. ID yang tidak ditemukan menghasilkan `404 Not Found`.

### DELETE `/api/buku/{id}`

Tidak memerlukan body. Response `200 OK` berisi objek buku yang dihapus di dalam `data`. ID yang tidak ditemukan menghasilkan `404 Not Found`.

Request dengan JSON tidak valid atau field buku tidak valid menghasilkan `400 Bad Request`; `Content-Type` yang tidak sesuai menghasilkan `415 Unsupported Media Type`. Respons API buku menggunakan bentuk:

```json
{
  "success": false,
  "message": "Field judul, penulis, penerbit, dan tahun_terbit wajib valid",
  "data": null
}
```

## Endpoint Web (bukan API JSON)

Route berikut adalah halaman/form web dan tidak memakai format response API. Aksesnya menggunakan sesi login (`AuthMiddleware`). Route web mahasiswa ditangani oleh `MahasiswaController`.

| Method | URL | Controller | Keterangan |
| --- | --- | --- | --- |
| GET | `/login` | `AuthController::loginForm()` | Form login |
| POST | `/login` | `AuthController::login()` | Login web berbasis sesi |
| GET | `/register` | `AuthController::registerForm()` | Form pendaftaran |
| POST | `/register` | `AuthController::register()` | Pendaftaran web; request JSON juga didukung |
| GET | `/logout` | `AuthController::logout()` | Mengakhiri sesi login |
| GET | `/mahasiswa` | `MahasiswaController::index()` | Daftar mahasiswa; mendukung query `search` dan `page` |
| GET | `/mahasiswa/create` | `MahasiswaController::create()` | Form tambah mahasiswa |
| POST | `/mahasiswa` | `MahasiswaController::store()` | Menyimpan mahasiswa dari form |
| GET | `/mahasiswa/{id}` | `MahasiswaController::show()` | Halaman detail mahasiswa |
| GET | `/mahasiswa/{id}/edit` | `MahasiswaController::edit()` | Form edit mahasiswa |
| POST | `/mahasiswa/{id}` | `MahasiswaController::update()` | Menyimpan perubahan dari form |
| POST | `/mahasiswa/{id}/delete` | `MahasiswaController::destroy()` | Menghapus mahasiswa dari form |

Route web tidak identik dengan API JSON: operasi web mengarahkan ulang ke halaman lain dan menggunakan sesi, sedangkan operasi tulis pada `/api/mahasiswa` memakai Bearer token serta JSON.

## Pengujian dan Dokumentasi dengan Postman

Collection request tersedia di [`api/postman_collection.json`](../api/postman_collection.json), sedangkan environment lokal siap impor tersedia di [`api/SI-Akademik-Local.postman_environment.json`](../api/SI-Akademik-Local.postman_environment.json).

1. Di Postman, pilih **Import** dan impor kedua file JSON tersebut.
2. Pilih environment **SI Akademik Local** dari dropdown environment. Periksa `base_url` dan sesuaikan bila URL instalasi lokal berbeda.
3. Isi `password` pada environment dengan kata sandi akun uji. Jika username/email pada environment sudah terdaftar, lewati request register; jika belum, jalankan **POST - Register pengguna**.
4. Jalankan **POST - Login (ambil token)**. Salin nilai `data.token` dari response ke variable `token` di environment, lalu simpan. Token berlaku selama 5 menit.
5. Jalankan request CRUD. GET mahasiswa tidak membutuhkan token; POST, PUT, dan DELETE memakai token Bearer. Pastikan `nim_uji` adalah NIM 9 digit yang belum terdaftar dan `mahasiswa_id` menunjuk data yang aman untuk diubah/dihapus.
6. Deskripsi handler dan contoh response sukses/gagal sudah tersedia pada request collection. Untuk menerbitkan dokumentasi, buka menu collection dan pilih **View Documentation**; salin tautan hasil publish untuk lampiran laporan.

Jangan membagikan environment yang berisi password atau token. File environment yang disertakan tidak menyimpan password maupun token; isi hanya pada Postman lokal.
