# Laporan Praktikum

## Pemeliharaan Foto Mahasiswa

Pada `MahasiswaController::update()`, setelah data berhasil diperbarui, foto lama dihapus jika nama berkas foto berubah. Penghapusan dilakukan melalui helper `removePhoto()`, yang memeriksa keberadaan berkas sebelum memanggil `unlink()`.

## Jadwal Backup Basis Data

Backup basis data `si_akademik` dilakukan pada setiap akhir sesi praktikum, sebelum sesi ditutup. Ekspor basis data dalam format SQL dan simpan secara lokal di `storage/backups/` dengan nama berkas `si_akademik_YYYY-MM-DD_HH-mm.sql`.

Catat pelaksanaan backup setiap sesi pada tabel berikut:

| Tanggal sesi | Nama berkas backup | Status |
| --- | --- | --- |
|  |  | Belum dilaksanakan |